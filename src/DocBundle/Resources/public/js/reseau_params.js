/**
 * Created by scouliba on 21/01/2016.
 */

function synchroneRequest (formName) {
    $(formName).on('submit', function(e) {
        //e.preventDefault();
        var $this = $(this);
        $('.modal-body').html('Génération en cours.');
        //$('.modal-footer').css('display': 'inline-block');
        /*$.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
            success: function(data) {
                $('.modal-body').html( data.statut );
                $('.label-default').html( 'V'+data.version );
            }
        });*/
    });
}