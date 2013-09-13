var $runs = [];
var $heights = {};
var $fields = {};
var $run_selector_id = '#run-selector';
var $height_selector_id = '#height-selector';
var $field_selector_id = '#field-selector';
var $svg_container = '#svg-container';
var $loading_image_id = '#loading-image';
var $svg_controls_id = '#svg-controls';
var $play_id = '#play';
var $pause_id = '#pause';
var $forward_id = '#step-forward';
var $back_id = '#step-back';
var $animationStepTracker;
var $zoomValue = 1;


$(function () {
	$($run_selector_id).change(function () { selectRun(); });
	$($height_selector_id).change(function () { selectHeight(); });
	$($field_selector_id).change(function () { selectField(); });
	$($loading_image_id).hide();

	$($run_selector_id).attr('disabled', 'disabled');
	$($height_selector_id).attr('disabled', 'disabled');
	$($field_selector_id).attr('disabled', 'disabled');

	$($play_id).click(function () { playAnimation(); });
	$($pause_id).click(function () { pauseAnimation(); });
	$($forward_id).click(function () { forwardAnimation(); });
	$($back_id).click(function () { backAnimation(); });

	populateRunSelector();

});

function getSVGDomObject() {
	return $($svg_container).find('svg')[0];
}

function populateRunSelector() {
	$.get($navigationBaseUrl + '/index/get_runs',
		function (data) {
			for (var i = 0; i < data.length; ++i) {
				var run = data[i];
				var run_time = run.date + " t" + run.time;
				if ($runs[run_time] == null) {
					$runs[run_time] = run;
				}
				if ($heights[run_time] == null) {
					$heights[run_time] = [];
				}
				var run_time_height = run_time + run.height;
				if ($heights[run_time_height] == null) {
					$heights[run_time_height] = true;
					$heights[run_time].push(run);
				}
				if ($fields[run_time_height] == null) {
					$fields[run_time_height] = [];
				}
				$fields[run_time_height].push(run);
			}
			populateRunSelectorElement();
		}
	);
}

function populateRunSelectorElement() {
	$($run_selector_id).find('option').remove().end().append('<option value="">--Select a Run--</option>').val(""); // clear the run selector
	for (var run_time in $runs) {
		$($run_selector_id).append('<option value="' + run_time + '">' + run_time + '</option>');
	}
	$($run_selector_id).removeAttr('disabled');
	$($height_selector_id).attr('disabled', 'disabled');
	$($field_selector_id).attr('disabled', 'disabled');
}

function selectRun() {
	$($height_selector_id).find('option').remove().end().append('<option value="">--Select a Height--</option>').val(""); // clear the run selector
	var run_time = $($run_selector_id).val();
	for (var index in $heights[run_time]) {
		var run = $heights[run_time][index];
		var run_time_height = run.date + " t" + run.time + run.height;
		$($height_selector_id).append('<option value="' + run_time_height + '">' + run.height + '</option>');
	}
	$($height_selector_id).removeAttr('disabled');
	$($field_selector_id).attr('disabled', 'disabled');
	removeSVGAnimation();
}

function selectHeight() {
	$($field_selector_id).find('option').remove().end().append('<option value="">--Select a Field--</option>').val(""); // clear the run selector
	var run_time_height = $($height_selector_id).val();
	for (var index in $fields[run_time_height]) {
		var run = $fields[run_time_height][index];
		var run_time_height_field = run_time_height + run.field;
		$($field_selector_id).append('<option value="' + run_time_height_field + '">' + run.field + '</option>');
	}
	$($field_selector_id).removeAttr('disabled');
	removeSVGAnimation();
}

function selectField() {
	var run_time_height_field = $($field_selector_id).val();
	var run = $fields[run_time_height_field];
	$($svg_container).css('display', 'none');
	$($loading_image_id).show();
	$($svg_container).load($navigationBaseUrl + '/index/get_svg_file/anim_212.svg',
		{filename: 'anim_212.svg'},
		function (data) {
			$($svg_container).show();
			$($svg_container).find('svg').width('100%').height('100%');
			$($loading_image_id).hide();
			$($svg_controls_id).show();
		}
	);
}

function playAnimation() {
	getSVGDomObject().unpauseAnimations();
	$animationStepTracker = null;


}

function pauseAnimation() {
	getSVGDomObject().pauseAnimations();
	if ($animationStepTracker == null) {
		$animationStepTracker = getSVGDomObject().getCurrentTime();
	}
}

function forwardAnimation() {
	pauseAnimation();
	$animationStepTracker += getTimeStep();
	getSVGDomObject().setCurrentTime($animationStepTracker);
}

function backAnimation() {
	pauseAnimation();
	$animationStepTracker -= getTimeStep();
	if ($animationStepTracker <= 0) {
		$animationStepTracker = getFrameCount() * getTimeStep();
	}
	getSVGDomObject().setCurrentTime($animationStepTracker);
}

function getTimeStep() {
	return 0.75;
}

function getFrameCount() {
	return $(getSVGDomObject()).find('image').length;
}

function removeSVGAnimation() {
	$($svg_container).html('');
	$($svg_controls_id).hide();
	$animationStepTracker = null;
	setZoom(1);
}

function setZoom(zoomVal) {
	$zoomValue = zoomVal;
	$.each($('.zoom-indicator'),
		function (index, element) {
			$(element).removeClass('bold-zoom');
		}
	);
	$('#zoom_' + $zoomValue).addClass('bold-zoom');
}

function getMaxZoomValue() {
	return $('.zoom-indicator').length;
}

function zoomIn() {
	alert("zoom doesn't work yet");
	var zoomVal = (getMaxZoomValue() <= $zoomValue) ? $zoomValue : $zoomValue + 1;
	setZoom(zoomVal);
}

function zoomOut() {
	alert("zoom doesn't work yet");
	zoomVal = (1 >= $zoomValue) ? $zoomValue : $zoomValue - 1;
	setZoom(zoomVal);
}