<?php


interface ModFormsToolsInterface
{

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     * @param bool  $skipEmpty
     *
     * @return mixed
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false);

    /**
     * @param       $message
     * @param array $placeholders
     *
     * @return mixed
     */
    public function lexicon($message, $placeholders = array());

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return mixed
     */
    public function failure($message = '', $data = array(), $placeholders = array());

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return mixed
     */
    public function success($message = '', $data = array(), $placeholders = array());

    /**
     * @param array $opts
     */
    public function loadResourceJsCss(array $opts = array());

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed
     */
    public function getChunk($name = '', array $properties = array());

    public function fromJson($string);

    public function sendEmail($email, $subject, $body = 'no body set');

}

/**
 * Class Tools
 */
class Tools implements ModFormsToolsInterface
{

    static $xpdo;

    /** @var array $config */
    public $config = array();
    /** @var modX $modx */
    protected $modx;
    /** @var ModForms $ModForms */
    protected $ModForms;
    /** @var $namespace */
    protected $namespace;

    /**
     * @param $modx
     * @param $config
     */
    public function __construct($modx, &$config)
    {
        $this->modx = $modx;
        $this->config =& $config;

        self::$xpdo = $modx;

        $corePath = $this->modx->getOption('modforms_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modforms/');
        /** @var ModForms $ModForms */
        $this->ModForms = $this->modx->getService(
            'ModForms',
            'ModForms',
            $corePath . 'model/modforms/',
            array(
                'core_path' => $corePath
            )
        );

        $this->namespace = $this->ModForms->namespace;
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /** @inheritdoc} */
    public function lexicon($message, $placeholders = array())
    {
        return $this->ModForms->lexicon($message, $placeholders);
    }

    /** @inheritdoc} */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        return $this->ModForms->failure($message, $data, $placeholders);
    }

    /** @inheritdoc} */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        return $this->ModForms->success($message, $data, $placeholders);
    }


    /**
     * @param array $opts
     */
    public function loadResourceJsCss(array $opts = array())
    {
        $opts = array_merge($this->config, $opts);
        $pls = $this->makePlaceholders($opts);

        if ($opts['jqueryJs']) {
            $this->modx->regClientScript(preg_replace($this->config['replacePattern'], '', '
				<script type="text/javascript">
					if(typeof jQuery == "undefined") {
						document.write("<script src=\"' . str_replace($pls['pl'], $pls['vl'], $opts['jqueryJs']) . '\" type=\"text/javascript\"><\/script>");
					}
   				</script>
			'), true);
        } else {
            $this->modx->regClientScript(preg_replace('#(\n|\t)#', '', '
				<script type="text/javascript">
					if (typeof jQuery == "undefined") {
						document.write("<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js\" type=\"text/javascript\"><\/script>");
					}
				</script>
				'), true);
        }

        if ($opts['frontendJs']) {
            $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $opts['frontendJs']));
        }
        if ($opts['frontendCss']) {
            $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $opts['frontendCss']));
        }

        $config = $this->modx->toJSON(array(
            'assetsBaseUrl' => str_replace($pls['pl'], $pls['vl'], $opts['assetsBaseUrl']),
            'assetsUrl'     => str_replace($pls['pl'], $pls['vl'], $opts['assetsUrl']),
            'actionUrl'     => str_replace($pls['pl'], $pls['vl'], $opts['actionUrl']),
            'selector'      => "{$opts['selector']}",
            'propkey'       => "{$opts['propkey']}",
            'action'        => "{$opts['action']}",
            'ctx'           => "{$this->modx->context->get('key')}",
            'validation'    => (array)$opts['validation'],
            'inputmask'     => (array)$opts['inputmask'],
            'realperson'    => (array)$opts['realperson'],
            'modal'         => (array)$opts['modal'],
        ));
        $this->modx->regClientScript(preg_replace($this->config['replacePattern'], '', '
			<script type="text/javascript">
				' . trim($opts['objectName']) . '.initialize(' . $config . ');
   			</script>
		'), true);
    }

    /**
     * @param array  $array
     * @param string $plPrefix
     * @param string $prefix
     * @param string $suffix
     * @param bool   $uncacheable
     *
     * @return array
     */
    public function makePlaceholders(
        array $array = array(),
        $plPrefix = '',
        $prefix = '[[+',
        $suffix = ']]',
        $uncacheable = true
    ) {
        return $this->ModForms->makePlaceholders($array, $plPrefix, $prefix, $suffix, $uncacheable);
    }


    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        return $this->ModForms->getOption($key, $config, $default, $skipEmpty);
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    public function flattenArray(array $array = array(), $prefix = '')
    {
        $outArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
            } else {
                $outArray[$prefix . $key] = $value;
            }
        }

        return $outArray;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        return $this->ModForms->explodeAndClean($array, $delimiter);
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        return $this->ModForms->cleanAndImplode($array, $delimiter);
    }

    /** @inheritdoc} */
    public function clearJson($string)
    {
        $string = preg_replace('/,\s*([\]}])/m', '$1', $string);
        $string = preg_replace("#[\']+#si", '"', preg_replace("#[\r\n\t]+#si", '', $string));

        return $string;
    }

    /** @inheritdoc} */
    public function fromJson($string)
    {
        $string = $this->clearJson($string);
        $array = json_decode($string, true);
        $jsonError = json_last_error();
        if ($jsonError != JSON_ERROR_NONE) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[ModForms] JSON Error: " . $jsonError . "\n" . $string);
        }

        return $array;
    }

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed|string
     */
    public function getChunk($name = '', array $properties = array())
    {
        if (class_exists('pdoTools') AND $pdo = $this->modx->getService('pdoTools')) {
            $output = $pdo->getChunk($name, $properties);
        } elseif (strpos($name, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $name);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);
            $output = $chunk->process($properties, $content);
        } else {
            $output = $this->modx->getChunk($name, $properties);
        }

        return $output;
    }

    /** @return array Fields Grid Client */
    public function getFieldsGridForms()
    {
        $fields = $this->getOption('fields_grid_forms', null,
            'id,name,selector,email', true);
        $fields .= ',id,name,selector,email,subject,actions';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    /**
     * Function for sending email
     *
     * @param string $email
     * @param string $subject
     * @param string $body
     *
     * @return void
     */
    public function sendEmail($email, $subject, $body = 'no body set')
    {
        if (!isset($this->modx->mail) || !is_object($this->modx->mail)) {
            $this->modx->getService('mail', 'mail.modPHPMailer');
        }
        $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->setHTML(true);
        $this->modx->mail->set(modMail::MAIL_SUBJECT, trim($subject));
        $this->modx->mail->set(modMail::MAIL_BODY, $body);
        $this->modx->mail->address('to', trim($email));
        if (!$this->modx->mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                'An error occurred while trying to send the email: ' . $this->modx->mail->mailer->ErrorInfo);
        }
        $this->modx->mail->reset();
    }


}