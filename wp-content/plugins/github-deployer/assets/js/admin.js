(function($){
    var ajaxUrl = (typeof gd_vars !== 'undefined' && gd_vars.ajaxurl) ? gd_vars.ajaxurl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '');

    function openFolderModal($input){
        var $overlay = $('<div class="gd-modal-overlay"><div class="gd-modal"><p>Loading...</p></div></div>');
        $('body').append($overlay);
        var $modal = $overlay.find('.gd-modal');

        function load(path){
            $.get(ajaxUrl, { action: 'gd_browse_dir', path: path }, function(resp){
                if(!resp.success){
                    $modal.html('<p>Error loading directory.</p>');
                    return;
                }
                render(resp.data.path, resp.data.folders);
            });
        }

        function render(path, dirs){
            var crumbs = buildBreadcrumb(path);
            var html = '<div class="gd-breadcrumb">'+crumbs+'</div>';
            html += '<div class="gd-folder-list">';
            if(dirs.length){
                dirs.forEach(function(d){
                    html += '<a href="#" data-path="'+d.path+'">'+d.name+'</a>';
                });
            } else {
                html += '<em>No subdirectories</em>';
            }
            html += '</div>';
            html += '<p><button class="button gd-choose-folder">Choose this folder</button> <button class="button gd-cancel">Cancel</button></p>';
            $modal.html(html);

            $modal.find('.gd-folder-list a').on('click', function(e){
                e.preventDefault();
                load($(this).data('path'));
            });
            $modal.find('.gd-breadcrumb a').on('click', function(e){
                e.preventDefault();
                load($(this).data('path'));
            });
            $modal.find('.gd-choose-folder').on('click', function(){
                $input.val(path);
                $overlay.remove();
            });
            $modal.find('.gd-cancel').on('click', function(){
                $overlay.remove();
            });
        }

        function buildBreadcrumb(path){
            var parts = path.split('/').filter(function(p){ return p.length; });
            var crumbPath = '';
            var html = '<a href="#" data-path="/">/</a>';
            parts.forEach(function(p){
                crumbPath += '/' + p;
                html += ' / <a href="#" data-path="'+crumbPath+'">'+p+'</a>';
            });
            return html;
        }

        $overlay.on('click', function(e){
            if($(e.target).hasClass('gd-modal-overlay')){
                $overlay.remove();
            }
        });

        load('/');
    }

    $(document).on('click', '.gd-find-folder', function(e){
        e.preventDefault();
        var $input = $(this).closest('form').find('input[name="folder"]');
        openFolderModal($input);
    });
})(jQuery);

