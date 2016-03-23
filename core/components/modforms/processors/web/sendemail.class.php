<?php

require_once dirname(__FILE__) . '/action.class.php';

class modFormsSendEmailProcessor extends modFormsActionProcessor
{
    /** @var ModForms $ModForms */
    public $ModForms;

    /** @var array $prop */
    public $prop;

    public function process()
    {
        $selector = $this->ModForms->getOption('selector', $this->prop);

        $rows = array();
        $q = $this->modx->newQuery('MfForm');
        $q->innerJoin('MfFormOption', 'Selectors', 'Selectors.form = MfForm.id');
        $q->innerJoin('MfFormOption', 'Emails', 'Emails.form = MfForm.id');
        $q->where(array(
            'Selectors.key'   => 'selector',
            'Selectors.value' => $selector,
            'Emails.key'      => 'email',
            'MfForm.active'   => true
        ));

        $q->sortby('MfForm.rank', 'ASC');
        $q->select("MfForm.subject, MfForm.body, Emails.value as email");
        $q->limit(0);
        if ($q->prepare() && $q->stmt->execute()) {
            $rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (function_exists('fastcgi_finish_request')) {
            echo $this->ModForms->success();
            session_write_close();
            fastcgi_finish_request();
        }
        
        foreach ($rows as $row) {
            $body = $this->ModForms->getOption('body', $row);
            $email = $this->ModForms->getOption('email', $row);
            if (!$body OR !$email) {
                continue;
            }

            $subject = $this->ModForms->getOption('subject', $row, $this->modx->getOption('site_name'), true);
            if ($modChunk = $this->modx->getObject('modChunk', $body)) {
                $body = $this->ModForms->getChunk($modChunk->get('name'), $this->getProperties());
            }

            $this->ModForms->sendEmail($email, $subject, $body);
        }

        return $this->ModForms->success();
    }

}

return 'modFormsSendEmailProcessor';