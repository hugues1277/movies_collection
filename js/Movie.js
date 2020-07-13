class MovieClass {

    constructor(){
        this.baseUrl = OC.generateUrl('/apps/movies_collection');
        this.movieKey = ["id","imdbId","title","originalTitle","director","genre","year","runtime","posterUrl","trailerUrl","synopsis","cast","rating","comment","listed"];
        this.movieKeyRequirement = {"title":"title","genre":"genre","posterUrl":"poster url"}
    }

    getMovieKey(){
        return this.movieKey;
    }

    getMovies(genre,callback){
        if(genre && this.genre != genre){
            Imdb.aboardAjaxGetImages();
            this.cleanMovies();
            this.genre = genre;
            this.page = 0;
        }else if(!callback){
            return;
        }
        var self=this;
        $.ajax({
            url: this.baseUrl + '/list/'+this.genre+'?page='+this.page,
            type: 'GET',
            contentType: 'application/json',
        }).done(function (response) {
            if(response.length){
                response.forEach(element => {
                    self.displayMovie(element)
                });
                self.page++;
                if(callback){
                    callback()
                }
            }
        }).fail(function (response, code) {
        });
    }

    cleanMovies(){
        $("#movie-collection").html("");
    }

    displayMovie(movie, prepend){
        if(prepend){
            $("#movie-collection").prepend(this.generateMovie(movie))
        }else{
            $("#movie-collection").append(this.generateMovie(movie))
        }
        Imdb.getImageImdb("img_"+movie.id, movie.posterUrl, movie.id)
        $('#'+movie.id).click(()=>{
            this.show(movie.id, (response)=>{
                this.displayMoviePreview(response)
            })
        })
    }

    generateMovie(movie){
        return `<div id="${movie.id}" class="card" style="display:none;">
                    ${(movie.listed==1?'<span class="check icon-check">✓</span>':'')}
                    <img id="img_${movie.id}" alt="${movie.title||''}">
                    ${movie.year!=0?`<span class="badge">${movie.year}</span>`:``}
                    <div class="progress" style="width: ${movie.rating||0}%;"></div>
                    <h2>${movie.title||''}</h2>
                </div>`;       
    }

    seach(search){
        Imdb.aboardAjaxGetImages();
        this.cleanMovies();
        this.genre="search"
        var self=this;
        $.ajax({
            url: this.baseUrl + '/search/'+search,
            type: 'GET',
            contentType: 'application/json',
        }).done(function (response) {
            if(response.length){
                response.forEach(element => {
                    self.displayMovie(element)
                });
            }else{
                $("#movie-collection").html('<p style="padding-left:30px">No result found.</p>')
            }
        }).fail(function (response, code) {
        });
    }

    /********************* CRUD */

    show(id, callback){
        $.ajax({
            url: this.baseUrl + '/movie/' + id,
            type: 'GET',
            contentType: 'application/json',
        }).done(function (response) {
            callback(response)
        }).fail(function (response, code) {
        });
    }
    create(movie){
        if(this.validMovie(movie)){ return; }
        var self=this;
        $.ajax({
            url: this.baseUrl + '/movie',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(movie)
        }).done(function (response) {
            self.displayMovie(response, true)
            displayMenu("movie-collection")
            if(self.genre!="all"){
                Movie.getMovies("all")
            }
        }).fail(function (response, code) {
            if(response.status==400){
                toast("This movie already exist.", 4);
            }else{
                toast("An error occurred.", 4);
            }
        });
    }
    validMovie(movie){
        for(let k in this.movieKeyRequirement){
            if(!movie[k].length){
                toast(`You need to complete ${this.movieKeyRequirement[k]}.`, 3);
                return 1;
            }
        }
    }
    update(movie){
        if(this.validMovie(movie)){ return; }
        var self=this;
        $.ajax({
            url: this.baseUrl + '/movie/' + movie.id,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(movie)
        }).done(function (response) {
            $('#'+movie.id).remove()
            displayMenu("movie-collection")
            self.displayMovie(movie, true)
        }).fail(function (response, code) {
            toast("An error occurred.", 3);
        });
    }
    updateListed(id, callback){
        $.ajax({
            url: this.baseUrl + '/movie/listed/' + id,
            type: 'PUT',
            contentType: 'application/json',
        }).done(function (response) {
            callback(response)
        }).fail(function (response, code) {
        });
    }
    destroy(id){
        var self=this;
        $.ajax({
            url: this.baseUrl + '/movie/' + id,
            type: 'DELETE',
            contentType: 'application/json',
        }).done(function (response) {
            $('#'+id).remove()
            toast("Movie deleted.", 3);
            self.hideMoviePreview();
        }).fail(function (response, code) {
        });
    }

    /****************************** show details */
    
    displayMoviePreview(movie) {

        let imgSrc = $('#img_'+movie.id).attr("src");
        let content = `
            <span id="icon-close"></span>
            <div class="col description_img">
                <img src="${imgSrc}">
            </div>
            <div class="col description">
                <h2>${movie.title}</h2>
                ${movie.originalTitle?`<p><span>Original Title:</span> ${movie.originalTitle}</p>`:``}
                ${movie.director?`<p><span>Director:</span> ${movie.director}</p>`:``}
                <p><span>Genre:</span> ${movie.genre}</p>
                ${movie.year!=0?`<p><span>Year:</span> ${movie.year}</p>`:``}
                ${movie.runtime!=0?`<p><span>Runtime:</span> ${movie.runtime}</p>`:``}
                ${movie.cast?`<p><span>cast:</span> ${movie.cast}</p>`:``}
                <div class="button-box">
                    ${movie.trailerUrl?`<a href="${movie.trailerUrl}" target="blank">Trailer</a>`:``}
                    <a href="https://www.youtube.com/results?search_query=${movie.title} trailer" target="blank">Y.</a>
                    <a href="#" id="update">Update</a>
                    <a href="#" class="${movie.listed==1?'icon-close':'icon-add'}" id="changeListed">My list</a>
                    <a id="add-new">Add new</a>
                    <a href="#" id="remove">Remove</a>
                </div>
                ${movie.comment?`<p><span>My Comment:</span> ${movie.comment}</p>`:``}
                <p><span>My rating:
                    <div class="progress-container">
                    </span> <div class="progress" style="width: ${movie.rating||0}%;"></div>
                    </div>
                </p>
                <br>
                ${movie.synopsis?`<p><span>Synopsis:</span> ${movie.synopsis}</p>`:``}
            </div>
            `
        $('#movie-preview').fadeIn(100).html(content)
        $('#icon-close').click(()=>{
            this.hideMoviePreview()
        })
        $('#update').click(()=>{
            this.show(movie.id,(response)=>{
                Form.addMovieToForm(response)
                Form.changeFormMode(false)
                displayMenu("movie-form")
            })
        })
        $('#remove').click(()=>{
            toast(`Are you sure? <button id="confirm-delete${movie.id}">Yes</button>`,6)
            $("#confirm-delete"+movie.id).click(()=>{
                this.destroy(movie.id)
            })
        })
        $('#changeListed').click(()=>{
            this.updateListed(movie.id,(movie)=>{
                if(movie.listed==1){
                    $('#'+movie.id).append('<span class="check icon-check">✓</span>')
                }else{
                    $('#'+movie.id+' .check').remove()
                }
                $('#changeListed').attr("class", movie.listed==1?'icon-close':'icon-add')
            })
        })
    }

    hideMoviePreview(){
        $('#movie-preview').fadeOut(100).html('')
    }
}

var Movie = new MovieClass();