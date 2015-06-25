/** Ajax task update manager. */

/*global jQuery,alert*/

var ContactLabTaskUpdateManager = function () {
    // my and that
    var my = {}, that = {};

    // Init.
    my.init = function () {
        Event.observe(document, 'dom:loaded', my.onLoad);
    };
    
    // Onload.
    my.onLoad = function () {
        var tasks = $$('span.task-status-running, span.task-status-new');
        if (tasks) {
            new PeriodicalExecuter(function (pe) {
                my.updateTasks(tasks);
            }, 5);
        }
    };
    
    my.updateTasks = function (tasks) {
        var ids = []; 
        tasks.each(function (el) {
            var id = parseInt(el.id.replace(/[a-z-]*/, ''));
            if (id) {
                ids.push(id);
            }
        });
        var sf = JSON.stringify(ids).replace(/"/g, '');
        new Ajax.Request(my.urls.getStatus, {
            method: 'post',
            parameters: 'ids=' + sf,
            loaderArea: false,
            onSuccess: function(response) {
                my.updateTasksSuccess(JSON.parse(response.responseText));
            }
        });
    };
    
    my.updateTasksSuccess = function (tasks) {
        tasks.each(function(item) {
            $(item.id).replace(item.html);
            $(item.id2).replace(item.statusHtml);
            $(item.id3).replace(item.actionsHtml);
        });
    };
    
    // Set urls
    that.setUrls = function (urls) {
        my.urls = urls;
    };
    
    that.setRequestStatusUrl = function (url) {
        my.updateRequestStatusUrl = url;
        
        $$('#task_id .pager')[0].insert('<div style="display: inline-block; margin-left: 50px" id="get-subscriber-data-exchange-status"/>');
        
        new PeriodicalExecuter(my.updateRequestStatus, 20);
        my.updateRequestStatus();
    };
    
    my.updateRequestStatus = function () {
        new Ajax.Request(my.updateRequestStatusUrl, {
            loaderArea: false,
            onSuccess: my.doUpdateRequestStatus
        });
    };
    
    my.doUpdateRequestStatus = function (response) {
        response = JSON.parse(response.responseText);
        $('get-subscriber-data-exchange-status').innerHTML = response.label;
    };
    
    // Calls init.
    my.init();
    
    return that;
};

