var highlighter = {
    canvasEl: '',
    canvas: null,

    init: function(container) {
        this.canvasEl = container;
        this.canvas = document.getElementById(container);

        this.canvas.addEventListener('mouseup', function(evt) {
            handleSelectedText(evt);
        });

        this.canvas.addEventListener('keyup', function(evt) {
            handleSelectedText(evt);
        });
    },

    getMousePos: function(canvas, evt) {
        var rect = canvas.getBoundingClientRect();
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    },

    showHighlighterMenu: function(coordX, coordY) {
        $('#imanee-highlighter-menu')
            .css({'top': coordY + 'px', 'left' : coordX + 'px' })
            .addClass('highlighter-menu-active')
        ;
    },

    hideHighlighterMenu: function() {
        $('#imanee-highlighter-menu')
            .removeClass('highlighter-menu-active')
        ;
    },

    onButtonClick: function(selectedText) {
        $('#quotable_image').html('<img src="/app_dev.php/imanee/highlighter/quote?text=' + selectedText + '">');
    }
};

function handleSelectedText(evt) {
    var selectedText = getSelectionText();
    if (selectedText) {
        var mousePos = highlighter.getMousePos(highlighter.canvas, evt);
        highlighter.showHighlighterMenu($('.post-content').width() + 20, mousePos.y);
        //console.log('position:' + mousePos.x + ',' + mousePos.y);
        //alert("Got selected text " + selectedText);
    } else {
        highlighter.hideHighlighterMenu();
    }
}

function getSelectionText() {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }

    return text;
}

$( '.js--toggle-highlight-mode' ).on( 'click', function ( ev ) {
    ev.preventDefault();

    $( 'body' ).toggleClass( 'highlight-mode' );

    if ( $( 'body' ).hasClass( 'highlight-mode' ) ) {
        var text = getSelectionText();
        highlighter.onButtonClick(text);
        setTimeout(function () {
            $('.js--highlight-panel-text'). val('"' + text + '"');
            $('.js--highlight-panel-text').focus();
        }, 10);

        // on escape key leave the search mode
        $( document ).on( 'keyup.highlightMode', function( ev ) {
            ev.preventDefault();
            if ( ev.keyCode === 27 ){
                $( 'body' ).toggleClass( 'highlight-mode' );
                $( document ).off( 'keyup.highlightMode' );
                highlighter.hideHighlighterMenu();
            }
        } );
    } else {
        $( document ).off( 'keyup.highlightMode' );
    }
} );