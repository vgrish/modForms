<?php

/**
 * The base class for ModForms.
 */
class ModForms
{
    /** @var modX $modx */
    public $modx;
    /** @var mixed|null $namespace */
    public $namespace = 'modforms';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();
    /** @var Tools $Tools */
    public $Tools;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modforms/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/modforms/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modforms/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'      => $this->namespace,
            'assetsBasePath' => MODX_ASSETS_PATH,
            'assetsBaseUrl'  => MODX_ASSETS_URL,

            'assetsUrl'    => $assetsUrl,
            'cssUrl'       => $assetsUrl . 'css/',
            'jsUrl'        => $assetsUrl . 'js/',
            'imagesUrl'    => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,

            'corePath'     => $corePath,
            'modelPath'    => $corePath . 'model/',
            'handlersPath' => $corePath . 'handlers/',

            'chunksPath'     => $corePath . 'elements/chunks/',
            'templatesPath'  => $corePath . 'elements/templates/',
            'snippetsPath'   => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'replacePattern'  => $this->getOption('replace_pattern', null, "#[\r\n\t]+#is"),
            'prepareResponse' => true,
            'jsonResponse'    => true,

        ), $config);

        $this->modx->addPackage('modforms', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modforms:default');
        $this->namespace = $this->getOption('namespace', $config, 'modforms');
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
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array  $scriptProperties
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));
        $this->modx->error->reset();
        if (!$this->Tools) {
            $this->loadTools();
        }
        if (!empty($this->initialized[$ctx])) {
            return true;
        }

        //$this->modx->log(1, print_r($this->config, 1));

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {
                   /* $config = $this->modx->toJSON(array(

                    ));

                    $script = "<script type=\"text/javascript\">ModFormsLexicon={$config}</script>";
                    if (!isset($this->modx->jscripts[$script])) {
                        $this->modx->regClientStartupScript($script, true);
                    }*/

                    $this->initialized[$ctx] = true;

                }
                break;
        }

        return true;
    }

    /**
     * Loads an instance of Tools
     *
     * @return boolean
     */
    public function loadTools()
    {
        if (!is_object($this->Tools) OR !($this->Tools instanceof ModFormsToolsInterface)) {
            $toolsClass = $this->modx->loadClass('tools.Tools', $this->config['handlersPath'], true, true);
            if ($derivedClass = $this->getOption('class_tools_handler', null, '')) {
                if ($derivedClass = $this->modx->loadClass('tools.' . $derivedClass, $this->config['handlersPath'],
                    true, true)
                ) {
                    $toolsClass = $derivedClass;
                }
            }
            if ($toolsClass) {
                $this->Tools = new $toolsClass($this->modx, $this->config);
            }
        }

        return !empty($this->Tools) AND $this->Tools instanceof ModFormsToolsInterface;
    }

    /**
     * @param       $message
     * @param array $placeholders
     *
     * @return string
     */
    public function lexicon($message, array $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }


    /** @inheritdoc} */
    public function getPropertiesKey(array $properties = array())
    {
        return !empty($properties['propkey']) ? $properties['propkey'] : false;
    }

    /** @inheritdoc} */
    public function saveProperties(array $properties = array())
    {
        return !empty($properties['propkey']) ? $_SESSION[$this->namespace][$properties['propkey']] = $properties : false;
    }

    /** @inheritdoc} */
    public function getProperties($key = '')
    {
        return !empty($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : array();
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * from
     * https://github.com/bezumkin/pdoTools/blob/19195925226e3f8cb0ba3c8d727567e9f3335673/core/components/pdotools/model/pdotools/pdotools.class.php#L320
     *
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
        $result = array('pl' => array(), 'vl' => array());
        $uncachedPrefix = str_replace('[[', '[[!', $prefix);
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result,
                    $this->makePlaceholders($v, $plPrefix . $k . '.', $prefix, $suffix, $uncacheable));
            } else {
                $pl = $plPrefix . $k;
                $result['pl'][$pl] = $prefix . $pl . $suffix;
                $result['vl'][$pl] = $v;
                if ($uncacheable) {
                    $result['pl']['!' . $pl] = $uncachedPrefix . $pl . $suffix;
                    $result['vl']['!' . $pl] = $v;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    public function runProcessor($action = '', $data = array())
    {
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath']) ? $this->config['processorsPath'] : MODX_CORE_PATH;
        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));

        return $this->config['prepareResponse'] ? $this->prepareResponse($response) : $response;
    }

    /**
     * This method returns prepared response
     *
     * @param mixed $response
     *
     * @return array|string $response
     */
    public function prepareResponse($response)
    {
        if ($response instanceof modProcessorResponse) {
            $output = $response->getResponse();
        } else {
            $message = $response;
            if (empty($message)) {
                $message = $this->lexicon('err_unknown');
            }
            $output = $this->failure($message);
        }
        if ($this->config['jsonResponse'] AND is_array($output)) {
            $output = $this->modx->toJSON($output);
        } elseif (!$this->config['jsonResponse'] AND !is_array($output)) {
            $output = $this->modx->fromJSON($output);
        }

        return $output;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed|string
     */
    public function getChunk($name = '', array $properties = array())
    {
        if (!$this->Tools) {
            $this->loadTools();
        }
        return $this->Tools->getChunk($name, $properties);
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
        if (!$this->Tools) {
            $this->loadTools();
        }
        $this->Tools->sendEmail($email, $subject, $body);
    }

}