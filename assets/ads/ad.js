window.onload = function() 
{
    var advertises = document.getElementsByClassName("MainAdverTiseMentDiv");
    var scripTags = document.getElementsByClassName("adScriptClass");
    var scripturl = scripTags[0].getAttribute('src');
    var siteurl =  scripturl.replace("/assets/ads/ad.js", "");
  
    var inx;
    for (inx = 0; inx < advertises.length; inx++) 
    
    {
        advertises[inx].setAttribute("style", "position:relative; z-index: 0; display:inline-block;");
        
        var getAdSize = advertises[inx].getAttribute('data-adsize');
        var getAdId =  advertises[inx].getAttribute('data-adid');
        var getAdTypeId =  advertises[inx].getAttribute('data-adType');
        var AdUrl = siteurl+'/ads/'+getAdId+'/'+getAdSize+'/'+getAdTypeId;
        var xhttp = new XMLHttpRequest();
        xhttp.customdata = advertises[inx];
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) 
            {
                this.customdata.innerHTML = this.responseText;
            }
        };
       
        xhttp.open('GET', AdUrl , true);
        xhttp.send();
    }
  
}
function hideAdverTiseMent(elem) 
{
 elem.parentElement.style.display = "none";
}



