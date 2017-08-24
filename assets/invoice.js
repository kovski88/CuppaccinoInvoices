
    $(document).ready(function(){
        //$('.advancedoptions').addClass("hidden");

	$(".advancedOptions").click(function () {

    $header = $(this);
    //getting the next element
    $content = $header.next();
    //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
    $content.slideToggle(500, function () {
        //execute this after slideToggle is done
        //change text of header based on visibility of content div
        $header.text(function () {
            //change text based on condition
            return $content.is(":visible") ? "Collapse -" : "Advanced Options +";
        });
    });

});

});

/*


	$header = $(this);
    	//getting the next element
    	$content = $header.next();

	
        $('.advancedOptions').click(function() {

            if ($content.hasClass("hidden")) {
                $(content).removeClass("hidden").addClass("visible");

            } else {
                $(content).removeClass("visible").addClass("hidden");
            }
        });
	
		$content.slideToggle(500, function () {
        	//execute this after slideToggle is done
	        //change text of header based on visibility of content div
        	$header.text(function () {
	            //change text based on condition
            	return $content.is(":visible") ? "Collapse" : "Expand";
        	});
    	});
    });
*/
