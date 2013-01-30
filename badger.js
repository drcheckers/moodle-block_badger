
YUI().use("node-base", function(Y) {
    var getbadge = function(e){
        assertion = e.currentTarget.getAttribute('data-assertion') 
        if(!assertion) assertion = e.currentTarget.get('dataset').assertion ;
        OpenBadges.issue(assertion, 
            function(errors, successes) { 
                //alert(errors.toSource()) 
                //alert(successes.toSource()) 
            });    
        };
    Y.on("click", getbadge, ".getbadge");
});
