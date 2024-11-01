var wpmbMedia;

(function($){
	$(function(){
		$("div.wpmb-expand-title").click(function(){
			var parentCanvas = $(this).parents("div.wpmb-canvas");
			
			$("div#wpmb-message").hide();
			
			parentCanvas.find("div.wpmb-summary-zone").hide(200, function(){
				parentCanvas.find("div.wpmb-detail-zone").show(400);
			});
		});

		$("div.wpmb-collapse-button").click(function(){
			var parentCanvas = $(this).parents("div.wpmb-canvas");
			
			$("div#wpmb-message").hide();
			
			parentCanvas.find("div.wpmb-detail-zone").hide(200, function(){
				parentCanvas.find("div.wpmb-summary-zone").show(400);
			});
		});
		
		$("div.wpmb-save-button").click(function(){
			var parentCanvas = $(this).parents("div.wpmb-canvas");
			
			$("div#wpmb-message").hide();
			wpmbEnableButtonsPostEdit(false);
			
			$.post( WpmbAjax.ajaxurl, {
				action : "save-media-data",
				id : parentCanvas.find("input#wpmb-data-media-id").val(),
				title : parentCanvas.find("input.wpmb-data-title").val(),
				description : parentCanvas.find("textarea.wpmb-data-description").val(),
				post_id : wpmbMedia.labels.postId
			}, function(response){
				if (response.result == 'done') {
					parentCanvas.find("div.wpmb-expand-title").html(
						parentCanvas.find("input.wpmb-data-title").val()+
						"<br />"+
						parentCanvas.find("input.wpmb-data-source-url").val()
					);
					$("div#wpmb-message").html(wpmbMedia.labels.doneSaveMsg).show();
				} else if (response.result == 'empty') {
					$("div#wpmb-message").html(wpmbMedia.labels.emptyMsg).show();
				} else {
					$("div#wpmb-message").html(wpmbMedia.labels.errorSaveMsg).show();
				}
				wpmbEnableButtonsPostEdit(true);
				parentCanvas.find("div.wpmb-detail-zone").hide(200, function(){
					parentCanvas.find("div.wpmb-summary-zone").show(400);
				});
			},"json");
		});
		
		$("div.wpmb-reset-button").click(function(){
			var parentCanvas = $(this).parents("div.wpmb-canvas");
			
			$("div#wpmb-message").hide();
			wpmbEnableButtonsPostEdit(false);
			
			$.post( WpmbAjax.ajaxurl, {
				action : "reset-media-data",
				id : parentCanvas.find("input#wpmb-data-media-id").val(),
				post_id : wpmbMedia.labels.postId
			}, function(response){
				if (response.title) {
					parentCanvas.find("input.wpmb-data-title").val(response.title);
					parentCanvas.find("textarea.wpmb-data-description").val(response.description);
					parentCanvas.find("div.wpmb-expand-title").html(
						parentCanvas.find("input.wpmb-data-title").val()+
						"<br />"+
						parentCanvas.find("input.wpmb-data-source-url").val()
					);
					$("div#wpmb-message").html(wpmbMedia.labels.doneResetMsg).show();
				} else if (response.error) {
					$("div#wpmb-message").html(wpmbMedia.labels.errorResetMsg).show();
				}
				wpmbEnableButtonsPostEdit(true);
				parentCanvas.find("div.wpmb-detail-zone").hide(200, function(){
					parentCanvas.find("div.wpmb-summary-zone").show(400);
				});
			},"json");
		});
		
		$("div.wpmb-activate-button").click(function(){
			var parentCanvas = $(this).parents("div.wpmb-canvas");
			
			$("div#wpmb-message").hide();
			$("div#wpmb-message").hide();
			wpmbEnableButtonsPostEdit(false);
			
			$.post( WpmbAjax.ajaxurl, {
				action : "activate-media",
				id : parentCanvas.find("input#wpmb-data-media-id").val(),
				post_id : wpmbMedia.labels.postId
			}, function(response){
				if (response.result == 'active') {
					parentCanvas.find("div.wpmb-expand-title").removeClass("wpmb-expand-title-desactivated");
					parentCanvas.find("div.wpmb-detail-zone").hide(200, function(){
						parentCanvas.find("div.wpmb-summary-zone").show(400);
					});
					$("div#wpmb-message").html(wpmbMedia.labels.doneActivateMsg).show();
					parentCanvas.find("div.wpmb-activate-button").hide();
					parentCanvas.find("div.wpmb-desactivate-button").show();
				} else if (response.result == 'inactive') {
					parentCanvas.find("div.wpmb-expand-title").addClass("wpmb-expand-title-desactivated");
					parentCanvas.find("div.wpmb-detail-zone").hide(200, function(){
						parentCanvas.find("div.wpmb-summary-zone").show(400);
					});
					$("div#wpmb-message").html(wpmbMedia.labels.doneDesactivateMsg).show();
					parentCanvas.find("div.wpmb-activate-button").show();
					parentCanvas.find("div.wpmb-desactivate-button").hide();
				} else {
					$("div#wpmb-message").html(wpmbMedia.labels.errorActiveMsg).show();
				}
				wpmbEnableButtonsPostEdit(true);
			},"json");
		});
		
		$("div.wpmb-desactivate-button").click(function(){
			var parentCanvas = $(this).parents("div.wpmb-canvas");
			parentCanvas.find("div.wpmb-activate-button").trigger("click");
		});
		
		wpmbEnableButtonsPostEdit = function(status){
			if (status) {
				$("div.wpmb-button-zone").show();
			} else {
				$("div.wpmb-button-zone").hide();
			}
		};
	});
	
	wpmbMedia = {
		labels: {}
	}
})(jQuery);