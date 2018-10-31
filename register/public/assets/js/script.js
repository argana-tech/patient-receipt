// JavaScript Document
function respondWithWindowSize() {
	getWindowSize();
	function getWindowSize() {
		var sW,sH,s;
		sW = window.innerWidth;
		sH = window.innerHeight;
		
		var currentRatio = sW / sH;
		var branchRatioSame = 1 /* 1:1 */
		var branchRatioMin = 1.333 /* 4:3 */
		var branchRatioMax = 1.777 /* 16:9 */
		
		currentRatios = currentRatio		
		
		if (currentRatio <= branchRatioSame) {
			$('html').css({
				fontSize: '0.8vh'
			});
		} else if (currentRatio > branchRatioMin && currentRatio < branchRatioMax) {
			$('html').css({
				fontSize: '1.0vh'
			});
		} else if (currentRatio <= branchRatioMin) {
			$('html').css({
				fontSize: '1.0vh'
			});
		} else if (currentRatio >= branchRatioMax) {
			$('html').css({
				fontSize: '1.0vh'
			});
		}
				
	}
};

$(function() {
    respondWithWindowSize();

    $(window).scroll(function() {
        respondWithWindowSize();
    });

    $(window).resize(function() {
        respondWithWindowSize();
    });
});
