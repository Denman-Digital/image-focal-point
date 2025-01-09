(function ($, Image_Focal_Point) {
	let focalX = null;
	let focalY = null;

	function initValues() {
		$hiddenInput = $("#focal_point_hidden_input");
		$overlay = $("#focal-point-overlay");
		$pin = $overlay.find(".pin");
		$resetBtn = $("#focal-point-reset");
		$updateBtn = $("#focal-point-set");
		$valueHolder = $("#focal-point-value");
		let _focalXY = $hiddenInput.val().replaceAll("%", "").split(" ");

		focalX = _focalXY[0];
		focalY = _focalXY[1];
	}
	function cancelFocus() {
		$(".media-frame-content, .media-sidebar").removeClass("show");
		$overlay.removeClass("show");
	}
	function closeOverlay() {
		$(".media-frame-content, .media-sidebar").removeClass("show");
		$overlay.removeClass("show");

		$hiddenInput.val(`${focalX}% ${focalY}%`).trigger("change");

		if (focalX != 50 && focalY != 50) {
			$valueHolder.html(`${focalX}% ${focalY}%`);
			$updateBtn.attr("value", Image_Focal_Point.labels.change);
			$resetBtn.removeClass("button-disabled").attr("aria-disabled", null);
		} else {
			$valueHolder.html(Image_Focal_Point.labels.default);
			$updateBtn.attr("value", Image_Focal_Point.labels.set);
			$resetBtn.addClass("button-disabled").attr("aria-disabled", "true");
		}
	}
	function setFocus() {
		$(".media-frame-content, .media-sidebar").addClass("show");
		$(".media-toolbar, .media-menu-item").css("z-index", 0);
		initValues();
		$pin.css({ left: `${focalX}%`, top: `${focalY}%` });
		$overlay.addClass("show");
	}
	function resetFocus() {
		if ($resetBtn.attr("aria-disabled") === "true") {
			return;
		}
		focalX = 50;
		focalY = 50;
		$pin.css({ left: `${focalX}%`, top: `${focalY}%` });
		$resetBtn.addClass("button-disabled").attr("aria-disabled", "true");
		closeOverlay();
	}
	$(document).on("click", "#focal-point-overlay .pin-field img", function (e) {
		const $this = $(this);
		let offset = $this.offset();
		let relX = e.pageX - offset.left;
		let relY = e.pageY - offset.top;

		focalX = Math.round((relX / $this.width()) * 100);
		focalY = Math.round((relY / $this.height()) * 100);
		$pin.css({ left: `${focalX}%`, top: `${focalY}%` });
	});

	Image_Focal_Point.setFocus = setFocus;
	Image_Focal_Point.resetFocus = resetFocus;
	Image_Focal_Point.cancelFocus = cancelFocus;
	Image_Focal_Point.closeOverlay = closeOverlay;
})(jQuery, Image_Focal_Point);
