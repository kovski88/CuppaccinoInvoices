
    $(document).ready(function(){
        $('.advancedOptions').addClass("hidden");

        $('.advancedOptions').click(function() {
            var $this = $(this);

            if ($this.hasClass("hidden")) {
                $(this).removeClass("hidden").addClass("visible");

            } else {
                $(this).removeClass("visible").addClass("hidden");
            }
        });
    });
