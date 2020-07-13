class ImdbClass {

    constructor(){
        this.baseUrl = OC.generateUrl('/apps/movies_collection');
        this.ajaxRequest = [];
    }

    searchImdb(search, callback){
        var self=this;
        document.getElementById("loading").style.visibility="visible"
        $.ajax({
            url: this.baseUrl + '/imdb/' + search,
            type: 'GET',
            contentType: 'application/json',
        }).done(function (response) {
            if(response.length==0){
                toast("No result found.", 3);
            }else{
                callback(response);
            }
            document.getElementById("loading").style.visibility="hidden"
        }).fail(function (response, code) {
            toast("An error occurred.", 3);
            document.getElementById("loading").style.visibility="hidden"
        });
    }

    getImageImdb(showId, url, cardId){
        if(!showId || !url){
            return;
        }
        let request = $.ajax({
            url: this.baseUrl + '/getImage?url='+encodeURIComponent(url),
            type: 'GET',
        }).done(function (response) {
            if(document.getElementById(showId)){
                document.getElementById(showId).src=response
                if(cardId)
                    document.getElementById(cardId).style.display="inline-block"
            }
        }).fail(function (response, code) {
        });
        this.ajaxRequest.push(request);
    }

    aboardAjaxGetImages(){
        this.ajaxRequest.forEach(element => {
            element.abort();
        });
        this.ajaxRequest = [];
    }
}

var Imdb = new ImdbClass();