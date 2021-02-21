<?php

class LibreNMSLMS extends LMS {

    public function SendSMS($number, $message, $messageid = null, $sms_options = null)
    {
        $msg_len = mb_strlen($message);

        if (!$msg_len) {
            return trans('SMS message is empty!');
        }

        $debug_phone = isset($sms_options['debug_phone']) ? $sms_options['debug_phone'] : ConfigHelper::getConfig('sms.debug_phone');
        if (!empty($debug_phone)) {
            $number = $debug_phone;
        }

        $prefix = isset($sms_options['prefix']) ? $sms_options['prefix'] : ConfigHelper::getConfig('sms.prefix', '');
        $number = preg_replace('/[^0-9]/', '', $number);
        $number = preg_replace('/^0+/', '', $number);

        $phone_number_validation_pattern = isset($sms_options['phone_number_validation_pattern'])
            ? $sms_options['phone_number_validation_pattern']
            : ConfigHelper::getConfig('sms.phone_number_validation_pattern', '', true);
        if (!empty($phone_number_validation_pattern) && !preg_match('/' . $phone_number_validation_pattern . '/', $number)) {
            return trans('Phone number validation failed!');
        }

        // add prefix to the number if needed
        if ($prefix && substr($number, 0, strlen($prefix)) != $prefix) {
            $number = $prefix . $number;
        }

        // message ID must be unique
        if (!$messageid) {
            $messageid = '0.' . time();
        }

        $message = preg_replace("/\r/", "", $message);

        $message = str_replace(
            array('%body'),
            array($message),
            isset($sms_options['message_template'])
                ? $sms_options['message_template']
                : ConfigHelper::getConfig('sms.message_template', '%body')
        );

        $transliterate_message = isset($sms_options['transliterate_message']) ? $sms_options['transliterate_message']
            : ConfigHelper::getConfig('sms.transliterate_message', 'false');
        if (ConfigHelper::checkValue($transliterate_message)) {
            $message = iconv('UTF-8', 'ASCII//TRANSLIT', $message);
        }

        $max_length = isset($sms_options['max_length']) ? $sms_options['max_length']
            : ConfigHelper::getConfig('sms.max_length');
        if (!empty($max_length) && intval($max_length) > 6 && $msg_len > intval($max_length)) {
            $message = mb_substr($message, 0, $max_length - 6) . ' [...]';
        }

        $service = isset($sms_options['service']) ? $sms_options['service'] : ConfigHelper::getConfig('sms.service');
        if (empty($service)) {
            return trans('SMS "service" not set!');
        }

        $errors = array();
        foreach (explode(',', $service) as $service) {
            $data = array(
                'number' => $number,
                'message' => $message,
                'messageid' => $messageid,
                'service' => $service,
                'transliterate_message' => $transliterate_message,
                'sms_options' => $sms_options,
            );

            // call external SMS handler(s)
          //  $data = $this->ExecHook('send_sms_before', $data);
          //  $data = $this->executeHook('send_sms_before', $data);

            if ($data['abort']) {
                if (is_string($data['result'])) {
                    $errors[] = $data['result'];
                    continue;
                } elseif (is_array($data['result'])) {
                    $errors = array_merge($errors, $data['result']);
                    continue;
                } else {
                    return $data['result'];
                }
            }

            $number = $data['number'];
            $message = $data['message'];
            $messageid = $data['messageid'];

            switch ($service) {
                case 'smstools':
                    $dir = isset($sms_options['smstools_outdir']) ? $sms_options['smstools_outdir']
                        : ConfigHelper::getConfig('sms.smstools_outdir', DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'spool' . DIRECTORY_SEPARATOR . 'sms' . DIRECTORY_SEPARATOR . 'outgoing');

                    if (!file_exists($dir)) {
                        $errors[] = trans('SMSTools outgoing directory not exists ($a)!', $dir);
                        continue 2;
                    }
                    if (!is_writable($dir)) {
                        $errors[] = trans('Unable to write to SMSTools outgoing directory ($a)!', $dir);
                        continue 2;
                    }

                    $filename = $dir . DIRECTORY_SEPARATOR . 'lms-' . $messageid . '-' . $number;

                    $headers = array();
                    $headers['To'] = $number;

                    $latin1 = iconv('UTF-8', 'ASCII', $message);
                    if (strlen($latin1) != mb_strlen($message, 'UTF-8')) {
                        $headers['Alphabet'] = 'UCS2';
                        $message = iconv('UTF-8', 'UNICODEBIG', $message);
                    }

                    $queue = isset($sms_options['queue']) ? $sms_options['queue']
                        : ConfigHelper::getConfig('sms.queue', '', true);
                    if (!empty($queue)) {
                        $headers['Queue'] = $queue;
                    }

                    $delivery_reports = isset($sms_options['delivery_reports']) ? $sms_options['delivery_reports']
                        : ConfigHelper::getConfig('sms.delivery_reports', 'false');
                    if (ConfigHelper::checkValue($delivery_reports)) {
                        $headers['Report'] = 'yes';
                    }

                    $header = '';
                    array_walk($headers, function ($value, $key) use (&$header) {
                        $header .= $key . ': ' . $value . "\n";
                    });

                    //$message = clear_utf($message);
                    $file = sprintf("%s\n%s", $header, $message);

                    if ($fp = fopen($filename, 'w')) {
                        fwrite($fp, $file);
                        fclose($fp);
                    } else {
                        $errors[] = trans('Unable to create file $a!', $filename);
                        continue 2;
                    }

                    return MSG_NEW;
                default:
                    $errors[] = trans('Unknown SMS service!');
                    continue 2;
            }
        }
        return implode(', ', $errors);
    }

    public function NotifyUsers(array $params)
    {
        global $LMS;

        $notification_attachments = ConfigHelper::checkConfig('phpui.helpdesk_notification_attachments');

        $notify_author = ConfigHelper::checkConfig('phpui.helpdesk_author_notify');
        $userid = Auth::GetCurrentUser();
        $sms_service = ConfigHelper::getConfig('sms.service');

        $args = array(
            'queue' => $params['queue'],
        );
        if (!$notify_author && $userid) {
            $args['user'] = $userid;
        }

        // send email
        $args['type'] = MSG_MAIL;

        $smtp_options = $this->GetRTSmtpOptions();

        if ($params['verifierid']) {
            $verifier_email = $this->DB->GetOne(
                'SELECT email FROM users WHERE email <> \'\' AND deleted = 0 AND access = 1 AND users.id = ?
                AND (ntype & ?) > 0',
                array($params['verifierid'], MSG_MAIL)
            );
            if (!empty($verifier_email)) {
                $params['mail_headers']['To'] = '<' . $verifier_email . '>';
                $LMS->SendMail(
                    $verifier_email,
                    $params['mail_headers'],
                    $params['mail_body'],
                    $notification_attachments && isset($params['attachments']) && !empty($params['attachments']) ? $params['attachments'] : null,
                    null,
                    $smtp_options
                );
            }
        }

        if ($params['queue']) {
            if ($recipients = $this->DB->GetCol(
                'SELECT DISTINCT email
			FROM users, rtrights
			WHERE users.id=userid AND queueid = ? AND email != \'\'
				AND (rtrights.rights & ' . RT_RIGHT_NOTICE . ') > 0 AND deleted = 0 AND access = 1'
                . (!isset($args['user']) || $notify_author ? '' : ' AND users.id <> ?')
                . ($params['verifierid'] ? ' AND users.id <> ' . intval($params['verifierid']) : '')
                . ' AND (ntype & ?) > 0',
                array_values($args)
            )) {
                if (isset($params['oldqueue'])) {
                    $oldrecipients = $this->DB->GetCol(
                        'SELECT DISTINCT email
					FROM users, rtrights
					WHERE users.id=userid AND queueid = ? AND email != \'\'
						AND (rtrights.rights & ' . RT_RIGHT_NOTICE . ') > 0 AND deleted = 0 AND access = 1
						AND (ntype & ?) > 0',
                        array($params['oldqueue'], MSG_MAIL)
                    );
                    if (!empty($oldrecipients)) {
                        $recipients = array_diff($recipients, $oldrecipients);
                    }
                }

                if (isset($params['attachments']) && !empty($params['attachments'])) {
                    if ($notification_attachments) {
                        $attachments = $params['attachments'];
                    } elseif ($params['contenttype'] == 'text/html') {
                        $attachments = array_filter($params['attachments'], function ($attachment) {
                            return isset($attachment['content-id']);
                        });
                    }
                }

                foreach ($recipients as $email) {
                    $params['mail_headers']['To'] = '<' . $email . '>';
                    $LMS->SendMail(
                        $email,
                        $params['mail_headers'],
                        $params['mail_body'],
                        isset($attachments) && !empty($attachments) ? $attachments : null,
                        null,
                        $smtp_options
                    );
                }
            }
        }

        // send sms
        $args['type'] = MSG_SMS;

        if ($params['verifierid']) {
            $verifier_phone = $this->DB->GetOne(
                'SELECT phone FROM users WHERE phone <> \'\' AND deleted = 0 AND access = 1 AND users.id = ?
                AND (ntype & ?) > 0',
                array($params['verifierid'], MSG_SMS)
            );
            if (!empty($verifier_phone)) {
                $this->SendSMS($verifier_phone, $params['sms_body']);
            }
        }

        if ($params['queue']) {
            if (!empty($sms_service) && ($recipients = $this->DB->GetCol(
                'SELECT DISTINCT phone
			FROM users, rtrights
				WHERE users.id=userid AND queueid = ? AND phone != \'\'
					AND (rtrights.rights & ' . RT_RIGHT_NOTICE . ') > 0 AND deleted = 0 AND access = 1'
                    . (!isset($args['user']) || $notify_author ? '' : ' AND users.id <> ?')
                    . ($params['verifierid'] ? ' AND users.id <> ' . intval($params['verifierid']) : '')
                    . ' AND (ntype & ?) > 0',
                array_values($args)
            ))) {
                if (isset($params['oldqueue'])) {
                    $oldrecipients = $this->DB->GetCol(
                        'SELECT DISTINCT phone
					FROM users, rtrights
					WHERE users.id=userid AND queueid = ? AND phone != \'\'
						AND (rtrights.rights & ' . RT_RIGHT_NOTICE . ') > 0 AND deleted = 0 AND access = 1
						AND (ntype & ?) > 0',
                        array($params['oldqueue'], MSG_SMS)
                    );
                    if (!empty($oldrecipients)) {
                        $recipients = array_diff($recipients, $oldrecipients);
                    }
                }

                foreach ($recipients as $phone) {
                    $this->SendSMS($phone, $params['sms_body']);
                }
            }
        }
    }
}

