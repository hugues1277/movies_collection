class FormClass {

    constructor(){
        this.baseUrl = OC.generateUrl('/apps/movies_collection');
        this.createMode = true;
        this.movieKey = [];
    }

    init(){
        this.movieKey = Movie.getMovieKey();
        var self=this;
        document.getElementById("search-imdb").addEventListener("submit", function(event){
            event.preventDefault()
            if(event.target[0].value){
                Imdb.searchImdb(event.target[0].value,(movie)=>{
                    self.addMovieToForm(movie);
                })
            }
        });
        document.getElementById("movie-insert-form").addEventListener("submit", function(event){
            event.preventDefault()
            let json = self.getFormToMovie()
            if(json){
                if(self.createMode){
                    Movie.create(json)
                }else{
                    Movie.update(json)
                }
            }
        });
        document.getElementById("posterUrl").addEventListener("input", function(event){
            event.preventDefault()
            Imdb.getImageImdb("posterUrlFormPreview",this.value)
        });
        document.getElementById("add-new").addEventListener("click", function(event){
            event.preventDefault()
            self.changeFormMode(true)
            self.cleanMovieForm()
            displayMenu("movie-form")
        });
    }

    /******************* form */

    addMovieToForm(json){
        this.movieKey.forEach(element => {
            if(json.hasOwnProperty(element)){
                document.getElementById(element).value = json[element];
            }
        });
        if(json.hasOwnProperty("plot")){
            document.getElementById("synopsis").value += "\n\n"+json["plot"];
        }
        Imdb.getImageImdb("posterUrlFormPreview",json["posterUrl"])
        document.getElementById("listed").checked = (json["listed"]==1?true:false);
    }

    getFormToMovie(){
        let json = {};
        this.movieKey.forEach(element => {
            json[element] = document.getElementById(element).value;
        });
        json['listed'] = (document.getElementById('listed').checked?1:0); 
        return json
    }

    changeFormMode(add) {
        if(add){
            this.createMode = true;
            $('#movie-insert-form #add').val('Add movie')
            $('#main-title').html('Add new movie')
        }else{
            this.createMode = false;
            $('#movie-insert-form #add').val('Update movie')
            $('#main-title').html('Update my movie')
        }
        $('#searchImdbInput').val('')
    }

    cleanMovieForm(){
        this.movieKey.forEach(element => {
            document.getElementById(element).value = '';
        });
        document.getElementById("posterUrlFormPreview").src = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D';
        document.getElementById("listed").checked = false;
    }

}

var Form = new FormClass();