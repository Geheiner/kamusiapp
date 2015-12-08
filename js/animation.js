//Global variables
var canvas = document.getElementById("animation");
var context = canvas.getContext("2d");
var width; // = canvas.width; //930;
var height; // = canvas.height; //550;
var animationID = -1;
var opacity_delta = 5;
var colours = [{r:235, g:166, b:52}, {r:128, g:128, b:128}];
var min_opacity = -500;
var max_opacity = 700;
var font_size = 30;
var interval_duration = 40;

var symbols =   ["قاموس", "ਕੋਸ਼", "字典", "orðabók", "מילון",
"tự điển", "kamus", "isichazamazwi", "k'amus", "maanatiira", "rimayqillqa",
"geiriadur", "ວັດຈະນານຸກົມ", "அகராதி", "शब्दकोश", "dictionnaire",
"qaamuus", "lexicon", "ლექსიკონი", "dicționar"];

var symbol_objects = new Array();

function start_animation() {
    var portal = document.getElementById("portal");

    if(portal.clientHeight > portal.clientWidth) { //Portrait mode (mobile style settings)
        canvas = document.getElementById('animation');
        canvas.setAttribute('width', '850');
        canvas.setAttribute('height', '1300');
        font_size = 70;
    }

    width = canvas.width;
    height = canvas.height;

    for(var index=0; index < symbols.length; index++) {
        var init_x = Math.random() * width;
        var init_y = Math.random() * height;
        var init_opacity = -100 * symbols.length + index * 100;
        symbol_objects[index] = new Symbol(symbols[index], init_x, init_y, init_opacity, index % 2);
    }

    if(animationID > -1)
        clearInterval(animationID);
    animationID = setInterval(animation, interval_duration);
}

function stop_animation() {
    if(animationID > -1)
        clearInterval(animationID);
    clear_canvas();
}

function pause_animation() {
    if(animationID > -1)
        clearInterval(animationID);
}

function continue_animation() {
    if(animationID > -1)
        clearInterval(animationID);
    animationID = setInterval(animation, interval_duration);
}

function animation() {
    clear_canvas();
    for(var index = 0; index < symbol_objects.length; index++) {
        symbol_objects[index].draw();
    }
}

function clear_canvas() {
    context.clearRect(0, 0, width, height);
}

function Symbol(symbol_text, x, y, opacity, colour) {
    var text = symbol_text, x_pos = x, y_pos = y, opacity = opacity, colour = colour;
    var delta = opacity_delta;

    this.draw = function () {
        if((opacity == max_opacity && delta > 0) || (opacity == min_opacity && delta < 0))
            delta = -delta;

        if(opacity == min_opacity) {
            x_pos = Math.random() * width;
            y_pos = Math.random() * height;
            colour = (colour + 1) % 2;
        }

        opacity += delta;

        context.font = font_size + "px" + "'Courier'";
        context.fillStyle = "rgba(" + colours[colour].r + "," + 
            colours[colour].g + "," + colours[colour].b + "," + opacity/1000.0 + ")";
        context.fillText(text, x_pos, y_pos);
    };
}

start_animation();
