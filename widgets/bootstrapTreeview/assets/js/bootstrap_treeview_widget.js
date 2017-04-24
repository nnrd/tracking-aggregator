/*
 * Author: Denis Semenov
 * E-mail: nonamenerd@gmail.com
 *
 * Description:
 * Bootstrap Treeview Widget
 */

BootstrapTreeviewWidget = (function (_this)
{
    var containerSelector;
    var options;
    var nodesState;

    var $widget;
    var $tree;
    var $model_name;
    var $model_id;
    var $right_panel;
    var $update_buttons;
    var $create_buttons;
    var $move_buttons;

    var stopBubble = false;

    /**
     * Init widget
     */
    function init(_options)
    {
        options = _options;
        if (
            typeof(options['actionUrl']) === 'undefined' ||
            typeof(options['containerId']) === 'undefined' ||
            typeof(options['strings']) === 'undefined'
        ) {
            throw new Error('Missing required oprion');
        }

        containerSelector = '#' + options['containerId'];
        nodesState = options['nodesState'];

        $widget = $(containerSelector);
        $create_buttons = $widget.find('.create');
        $tree = $widget.find('.tree');

        updateTree();

        // Setup controls if not readonly mode
        if (!options['readOnly'])
        {
            bindCreateControls();

            // Setup adding node updation controls after/outside treeveiw.
            // This is essential because treeview updates tree entirely on every event
            $widget.click(function(event) {
                updateControls();
                initMovable();
            });

            updateControls();
            initMovable();

            // Catch events before treeview to preserve our editing controls
            $widget[0].addEventListener('click', function(event) {
                return;
                var $target = $(event.target);
                if ($target.hasClass('stop-bubble') || stopBubble)
                {
                    // Event doesn't go to treeview
                    event.preventDefault();
                    event.cancelBubble = true;
                };
            }, true);
        }

        if (typeof(options['onInit']) === 'function')
        {
            options['onInit']($tree, _this);
        }
    }

    /**
     * Setup druggable on treeview elements
     */
    function initMovable()
    {
        $tree.find('li').each(function(i,e) {
            $(e)
            .draggable({
                containment: 'parent',
                revert: true,
                helper: "clone"
            })
            .droppable({
                accept: containerSelector + " .treeview li",
                classes: {
                    "ui-droppable-active": "ui-state-active",
                    "ui-droppable-hover": "ui-state-hover"
                },
                drop: function( event, element ) {
                    var sourceNodeId = $tree.treeview('getNode', element.draggable.data('nodeid')).model_id;
                    var destinationNodeId = $tree.treeview('getNode', $(event.target).data('nodeid')).model_id;

                    request('move', {
                        source_id: sourceNodeId,
                        dest_id: destinationNodeId,
                        type: 'subnode'
                    }, function(data) {
                        updateTree(data);
                    });
                }
            });
        });

    }

    /**
     * Enable all controls after request is processed or failed
     */
    function enableControls()
    {
        $create_buttons.find('button').prop('disabled', false);
        $tree.find('.input-field, .update-button').prop('disabled', false);
        stopBubble = false;
        if ( typeof(options['onControlsEnable']) === 'function' )
        {
            options['onControlsEnable']($tree);
        }
    }

    /**
     * Disable all controls before request is processed or failed to prevent bouncing etc
     */
    function disableControls()
    {
        $create_buttons.find('button').prop('disabled', true);
        $tree.find('.input-field, .update-button').prop('disabled', true);
        stopBubble = true; // Catch all events - dont let'em go to treeview
        if ( typeof(options['onControlsDisable']) === 'function' )
        {
            options['onControlsDisable']($tree);
        }
    }

    /**
     * Update selected treeview elements to install updation controls
     */
    function updateControls()
    {
        $tree.find('.node-selected').each(function(index, element) {
            addUpdateControls($(element));
        });
    }

    /**
     * Update whole tree with new data, select specific node id if reqired
     *
     * @param data Object|undefined Tree data. If skipped data will be requested form server
     * @param select_node_id int|undefined Node id to select. If skiped no node will be selected
     */
    function updateTree(data, select_node_id)
    {
        function initTree(data, select_node_id) {
            $tree.treeview({
                data: data,
                expandIcon: 'glyphicon glyphicon-folder-close',
                collapseIcon: 'glyphicon glyphicon-folder-open',
                emptyIcon: 'glyphicon glyphicon-file'
            });

            $tree.treeview('expandAll');
            $tree.on('nodeSelected', onSelect);
            $tree.on('nodeUnselected', onUnselect);

            if (typeof(select_node_id) !== 'undefined')
            {
                $tree.treeview('selectNode', select_node_id);
                if (!options['readOnly'])
                {
                    updateControls();
                }
            }

            if (!options['readOnly'])
            {
                initMovable();
            }

            if ( typeof(options['onTreeUpdate']) === 'function' )
            {
                options['onTreeUpdate']($tree);
            }

        }

        enableControls();

        if (typeof(data) !== 'undefined')
        {
            $tree.treeview('remove');
            initTree(data, select_node_id);
        }
        else
        {
            request('refresh', null, function(data)
            {
                if ($tree.hasClass('treeview'))
                {
                    $tree.treeview('remove');
                }
                initTree(data);
            });
        }
    }

    /**
     * Perform a request of operation on server
     *
     * @param action string One of update|delete|move|create
     * @param data Object Data to be passed to server method, see widget action docs
     * @param callback function(data) Callback fired after success
     */
    function request(action, data, callback)
    {
        var p = $.post(options['actionUrl'], {
            action: action,
            params: data,
            nodesState: nodesState,
            rootNodeId: options['rootNodeId'],
            showRoot: options['showRoot']
        });

        p.done(function(response) {
            var json = JSON.parse(response);
            if (json.result == 'success') {
                callback(json.data);
            } else {
                enableControls();
            }
        });

        p.error(function() {
            enableControls();

        });
    }

    /**
     * Perform a delete node operation
     */
    function deleteNode(node)
    {
        if (confirm(options['strings']['confirm_delete_message']))
        {
            disableControls();
            request('delete', { model_id: node.model_id }, function (tree) {
                updateTree(tree, node.parentId);
            });
        }
    }

    /**
     * Perform an update node operation
     *
     * @param text string New node name
     */
    function updateNode(node, text)
    {
        disableControls();
        request('update', { model_id: node.model_id, text: text}, function (tree) {
            updateTree(tree, node.nodeId);
        });
    }

    /**
     * Install updation controlls on a specific element
     *
     * @param $element Object JQuery wrapped DOM element
     */
    function addUpdateControls($element)
    {
        function resetText()
        {
            $input.val(text);
            $input.focus();
            $input[0].setSelectionRange(text.length, text.length);
        }

        var $input = $element.find('.input-field');
        if ($input.length == 0)
        {
            var button_cancel = '<span class="update-button stop-bubble cancel glyphicon glyphicon-repeat"></span>';
            var button_edit = '<span class="update-button stop-bubble edit glyphicon glyphicon-ok"></span>';
            var button_remove = '<span class="update-button stop-bubble remove glyphicon glyphicon-remove"></span>';

            var html = $element.html();
            var text = $element.text();
            var nodeId = $element.attr('data-nodeId');
            var node = $tree.treeview('getNode', nodeId);
            var lastIndex = html.lastIndexOf(text);

            // Some wierd moves to sneak our controls inside a tree element
            // stop-bubble class prevents from bubbling events to treeview
            $element.css('display', 'flex').css('align-items', 'baseline');
            $element.html(html.substr(0, lastIndex) + '<input class="stop-bubble input-field" type="text">' + button_cancel + button_edit + button_remove);
            $element.find('.node-icon').css('width', 0);

            $input = $element.find('.input-field');
            $input.css('background-color', $element.css('background-color'));

            $element.find('.edit').click(function() {
                updateNode($tree.treeview('getNode', nodeId), $input.val().trim());
            });

            $element.find('.remove').click(function(){
                deleteNode($tree.treeview('getNode', nodeId));
            });

            $element.find('.cancel').click(function(){
                resetText();
            });

            $input.keyup(function(event) {
                var code = event.which;
                switch (code)
                {
                case 13:
                    updateNode($tree.treeview('getNode', nodeId), $input.val().trim());
                    break;
                case 27:
                    resetText();
                    break;
                }
            });

            resetText();
        }
    }

    /**
     * Bind create node buttons
     */
    function bindCreateControls()
    {
        function create(type)
        {
            var node = $tree.treeview('getSelected');
            var model_id = node.length ? node[0].model_id : -1;
            disableControls();
            request( 'create', { model_id: model_id, type: type }, function (tree) {
                if (node.length)
                {
                    updateTree(tree, node[0].nodeId);
                }
                else
                {
                    updateTree(tree);
                }
                enableControls();
            });
        }

        $create_buttons.show().removeClass('hidden');
        $create_buttons.find('.button-new-root').unbind().click(function() { create('root'); });
        $create_buttons.find('.button-new-node').unbind().click(function() { create('node'); });
        $create_buttons.find('.button-new-subnode').unbind().click(function() { create('subnode'); });
    }

    /**
     * OnSelect event of treeview
     */
    function onSelect(event, element) {
        if ( typeof(options['onSelect']) === 'function' )
        {
            options['onSelect']($tree, element);
        }

    }
    /**
     * OnUnselect event of treeview
     */
    function onUnselect(event, element) {
        if ( typeof(options['onUnselect']) === 'function' )
        {
            options['onUnselect']($tree, element);
        }
    }

    /**
     * Find tree node by a given model id
     */
    function findNodeByModelId(model_id) {
        var nodes = $tree.treeview('getEnabled');
        for(var i=0; i<nodes.length; i++)
        {
            if (nodes[i].model_id == model_id)
            {
                return nodes[i];
            }
        }
        return false;
    }

    /**
     * Silently select node by a given model id
     */
    function selectNodeByModelId(model_id)
    {
        var node = findNodeByModelId(model_id);
        if (node)
        {
            $tree.treeview('selectNode', [ node.nodeId, {silent: true}]);
        }
    }

    /**
     * Silently unselect all selected nodes
     */
    function unselectAll()
    {
        var nodes = $('.treeview').treeview('getSelected');
        for(var i=0; i<nodes.length; i++)
        {
            $tree.treeview('unselectNode', [ nodes[i].nodeId, {silent: true}]);
        }
    }

    // internal exports
    _this.init = init;
    _this.initMovable = initMovable;
    _this.selectNodeByModelId = selectNodeByModelId;
    _this.unselectAll = unselectAll;

    return _this;

} ( typeof BootstrapTreeviewWidget !== 'undefined' ? BootstrapTreeviewWidget : {} ));

if( typeof module !== 'undefined' ) { module.exports = BootstrapTreeviewWidget; }
