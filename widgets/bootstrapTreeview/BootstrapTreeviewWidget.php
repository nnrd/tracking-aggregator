<?php
namespace app\widgets\bootstrapTreeview;

use Yii;


/**
 * Bootstrap Treeview widget to handle yii2-nested-sets tree
 *
 * @author Denis Semenov <nonamenerd@gmail.com>
 */
class BootstrapTreeviewWidget extends \yii\bootstrap\Widget
{
    /**
     * Widget constants
     */
    const DEFAULT_NAME_ATTRIBUTE = 'title';
    const DEFAULT_ACTION_NAME = 'manageTree';
    const DEFAULT_WIDGET_ID_PREFIX = 'boorstrap-treeview-widget-';

    /**
     * @var string Nested-sets model class name
     */
    public $modelClass;

    /**
     * @var string Nested-sets model attribute name
     */
    public $nameAttribute = self::DEFAULT_NAME_ATTRIBUTE;

    /**
     * @var array Preset nodes state by model id e.g. [ '42' => ['selected' => true]]
     */
    public $nodesState;

    /**
     * @var callable Callback to sort resulting tree.
     */
    public $sortCallback;

    /**
     * @var JsExpression Js widget callbacks
     */
    public $onInit = false;
    public $onSelect = false;
    public $onUnselect = false;
    public $onTreeUpdate = false;
    public $onControlsEnable = false;
    public $onControlsDisable = false;

    /**
     * @var bool Readonly mode
     */
    public $readOnly = false;

    /**
     * @var string Controller action url
     */
    public $actionUrl;

    /**
     * @var bool Root node to show, if false show all root nodes
     */
    public $rootNodeId = false;

    /**
     * @var bool Show root node when $rootNodeId is true
     */
    public $showRoot = false;


    protected $tree;
    protected $model;
    protected $containerId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::registerTranslations();
        $this->model = new $this->modelClass;
        $this->containerId = self::DEFAULT_WIDGET_ID_PREFIX . rand();

        if(!isset($this->actionUrl)) {
            $this->actionUrl = \yii\helpers\Url::to([Yii::$app->controller->id . '/' . self::DEFAULT_ACTION_NAME]);
        }
    }

    /**
     * @inheritdoc
     */
    public static function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['widgets/bootstrap_treeview/*'] = [
            'class' => \yii\i18n\PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/widgets/bootstrap_treeview/messages',
            'fileMap' => [
                'widgets/bootstrap_treeview/messages' => 'messages.php',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('widgets/bootstrap_treeview/' . $category, $message, $params, $language);
    }

    /**
     * Shortcut for self::t('messages',...)
     *
     * @return string I18N message
     */
    public static function m($message, $params = [], $language = null)
    {
        return self::t('messages', $message, $params, $language);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('index', [
            'model'                 => $this->model,
            'options'               => [
                'name'              => $this->nameAttribute,
                'containerId'       => $this->containerId,
                'actionUrl'         => $this->actionUrl,
                'onInit'            => $this->onInit,
                'onSelect'          => $this->onSelect,
                'onUnselect'        => $this->onUnselect,
                'onTreeUpdate'      => $this->onTreeUpdate,
                'onControlsEnable'  => $this->onControlsEnable,
                'onControlsDisable' => $this->onControlsDisable,
                'readOnly'          => $this->readOnly,
                'nodesState'        => $this->nodesState,
                'rootNodeId'        => $this->rootNodeId,
                'showRoot'          => $this->showRoot,
                'strings'           => [
                    'confirm_delete_message' => self::m('Delete node?'),
                ]
            ],
        ]);
    }

    /**
     * Shortcut for controller actions
     *
     * @return array Actions to be put in a controller
     */
    public static function actions($params)
    {
        return [
            self::DEFAULT_ACTION_NAME => [
                'class' => actions\ManageTreeAction::class,
                'modelClass'    => $params['modelClass'],
                'nameAttribute' => isset($params['nameAttribute']) ? $params['nameAttribute'] : self::DEFAULT_NAME_ATTRIBUTE,
            ],
        ];
    }

    /**
     * Get data from the model and compose a tree to be passed to a bootstrap_treeview
     *
     * @return array Tree data
     */
    public function composeTree()
    {
        $result = [];

        if ($this->rootNodeId)
        {
            if ($this->showRoot)
            {
                $rootNodes = [$this->model->findOne(['id' => $this->rootNodeId])]; // Show one root
            }
            else
            {
                $rootNodes = $this->model->findOne(['id' => $this->rootNodeId])->children(1)->all(); // Show root subnodes
            }
        }
        else
        {
            $rootNodes = $this->model->find()->roots()->all();
        }



        foreach($rootNodes as $item)
        {
            $result[] = $this->composeNode($item);
        }
        if ($this->sortCallback)
        {
            return $this->sortCallback($result);
        }
        return $result;
    }

    /**
     * Reqursively walk and compose tree data
     *
     * @return array Tree data of a current level
     */
    protected function composeNode($node)
    {
        $subnodes = [];
        foreach($node->children(1)->all() as $subnode) {
            $subnodes[] = $this->composeNode($subnode);
        }

        $result = [
            'text' => $node->getAttribute($this->nameAttribute),
            'href' => '#node-' . $node->id,
            'model_id' => $node->id,
        ];
        if (is_array($this->nodesState) && array_key_exists($node->id, $this->nodesState))
        {
            $result['state'] = $this->nodesState[$node->id];
        }
        if ($subnodes)
        {
            $result['nodes'] = $subnodes;
        }

        return $result;
    }

}
