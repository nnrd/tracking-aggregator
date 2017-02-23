$right_panel = $('.rightpanel');
$update_buttons = $right_panel.find('.update');
$move_buttons = $right_panel.find('.move');
$model_name = $widget.find('.model-name');
$model_id = $widget.find('.model-id');

function showUpdateControls()
{
    $update_buttons.show().removeClass('hidden');
    $model_name.prop('disabled', false);
    if (moveNode)
        {
            $tree.treeview('selectNode', moveNode.nodeId);
        }
    bindUpdateControls();
}

function bindUpdateControls()
{
    $update_buttons.find('.button-update').unbind().click(function() {
        var node = $tree.treeview('getSelected');
        if (node.length) {
            disableControls();
            request( 'update', { model_id: node[0].model_id, text: $model_name.val() }, function (tree) {
                updateTree(tree, node[0].nodeId);
                initMovable();
            });
        }
    });

    $model_name.unbind().keyup(function(event) {
        var code = event.which;
        if (code == 13)
            {
                $update_buttons.find('.button-update').click();
            }
    });

    $update_buttons.find('.button-delete').unbind().click(function() {
        var node = $tree.treeview('getSelected');
        if (node.length) {
            if (confirm('Delete node?'))
                {
                    disableControls();
                    request( 'delete', { model_id: node[0].model_id }, function (tree) {
                        updateTree(tree, node[0].parentId);
                    });
                }
        }
    });

    $update_buttons.find('.button-move-before').unbind().click(function() {
        moveModeOn('before');
    });

    $update_buttons.find('.button-move-after').unbind().click(function() {
        moveModeOn('after');
    });

    $update_buttons.find('.button-move-subnode').unbind().click(function() {
        moveModeOn('subnode');
    });
}

function hideUpdateControls()
{
    $update_buttons.hide().addClass('hidden');
    $model_name.prop('disabled', true);
}


function showMoveControls()
{
    $move_buttons.show().removeClass('hidden');
    bindMoveControls();
}

function bindMoveControls()
{
    $move_buttons.find('.button-cancel').unbind().click(function() {
        moveModeOff();
    });

    $move_buttons.find('.button-done').unbind().click(function() {
        var node = $tree.treeview('getSelected');
        if (node.length && moveMode)
            {
                disableControls();
                request('move', {
                    source_id: moveNode.model_id,
                    dest_id: node[0].model_id,
                    type: moveMode
                }, function(tree) {
                    moveModeOff();
                    updateTree(tree);
                });
            }
    });
}

function hideMoveControls()
{
    $move_buttons.hide().addClass('hidden');
}
