(function($){
    $(function(){
        /*
         * Play video label : click
         *
         * Open the playing zone with video embeded.
         * If video embeded is already open, close the playing zone and
         * open the source url in a new window.
         */
        $("div.wpmb-tools-play-label").click(function(){
            var securityRightSpacing = 2;
            var parentCanvas = $(this).parents("div.wpmb-canvas");
            var videoWidth = parentCanvas.width() - securityRightSpacing; // Max video width.
            var videoHeight = parseInt((videoWidth * 9) / 16); // Proportionnal height for 16:9 display.
            
            // Changing the title attribute of the title.
            parentCanvas.find("a.wpmb-title").attr("title", parentCanvas.find("div.wpmb-tools-new-window-label").attr("title"));

            $(this).hide();
            parentCanvas.find("div.wpmb-tools-stop-label").show();
            
            parentCanvas.find("div.wpmb-thumbnail-zone").hide("normal", function() {
                parentCanvas.find("div.wpmb-playing-zone").show();
                
                $.post( WpmbAjax.ajaxurl, {
                    action : 'get-embedded-object',
                    mediaID: parentCanvas.find("input#wpmb-media-id").val(),
                    mediaWidth: videoWidth,
                    mediaHeight: videoHeight,
                    postID: parentCanvas.find("input#wpmb-post-id").val()
                }, function(response){
                    parentCanvas.find("div.wpmb-playing-zone").html(response);
                });
            });
        });

        /*
         * Close video label : click
         *
         * Close the playing zone with video embeded and show the thumbnail.
         */
        $("div.wpmb-tools-stop-label").click(function(){
            var parentCanvas = $(this).parents("div.wpmb-canvas");

            // Changing the title attribute of the title.
            parentCanvas.find("a.wpmb-title").attr("title", parentCanvas.find("div.wpmb-tools-play-label").attr("title"));

            $(this).hide();
            parentCanvas.find("div.wpmb-tools-play-label").show();

            parentCanvas.find("div.wpmb-playing-zone").hide("fast", function(){
                parentCanvas.find("div.wpmb-playing-zone").html(parentCanvas.find("div.wpmb-hidden-holder").html());
                parentCanvas.find("div.wpmb-thumbnail-zone").show("normal");
            });
        });

        /*
         * Video label : click
         *
         * Open the video zone provider (author) url in a new window.
         */
        $("div.wpmb-tools-author-label").click(function(){
            window.open($(this).parents("div.wpmb-canvas").find("input#wpmb-product-author-url").val());
        });

        /*
         * Open source label : click
         *
         * Open the source url in a new window.
         */
        $("div.wpmb-tools-new-window-label").click(function(){
            var parentCanvas = $(this).parents("div.wpmb-canvas");
            if (parentCanvas.find("div.wpmb-playing-zone").is(':visible')) {
                parentCanvas.find("div.wpmb-tools-stop-label").trigger("click");
            }
            window.open(parentCanvas.find("input#wpmb-source-url").val());
        });

        /*
         * Video Thumbnail : click
         *
         * Trigger de playing video label click.
         */
        $("img.wpmb-thumbnail").click(function(){
            var parentCanvas = $(this).parents("div.wpmb-canvas");
            parentCanvas.find("div.wpmb-tools-play-label").trigger("click");
        });

        /*
         * Video Title : click
         *
         * Open the playing zone with video embeded.
         * If video embeded is already open, close the playing zone and
         * open the source url in a new window.
         */
        $("a.wpmb-title").click(function(){
            var parentCanvas = $(this).parents("div.wpmb-canvas");
            if (parentCanvas.find("div.wpmb-playing-zone").is(':visible')) {
                parentCanvas.find("div.wpmb-tools-stop-label").trigger("click");
                parentCanvas.find("div.wpmb-tools-new-window-label").trigger("click");
            } else {
                parentCanvas.find("div.wpmb-tools-play-label").trigger("click");
            }
        });
    });
})(jQuery);