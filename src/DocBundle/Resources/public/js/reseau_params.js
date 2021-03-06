/**
 * Created by scouliba on 21/01/2016.
 */

function supprimer(url) {
    $('#confirm-delete .modal-body').html('Confirmez-vous la suppression ?');
    $('#confirm-delete').modal('show');
    $('.btn-ok-dialog').click(function(){
        $('#confirm-delete').modal('hide');
        $.getJSON(url, function (data) {
            if (data.status == 'deleted') {
                $('#param_'+data.id).remove();
                $('#versioni').html(data.version+" En cours");
            }
            $('#param_'+data.id).addClass('danger');
            $('#param_'+data.id+' .deleting').removeClass('hide');
            $('#param_'+data.id+' .not-deleting').addClass('hide');
        });
    });
}

function annuler(url) {
    $('#confirm-delete .modal-body').html('Confirmez-vous cette annulation?');
    $('#confirm-delete').modal('show');
    $('.btn-ok-dialog').click(function(){
        $('#confirm-delete').modal('hide');
        $.getJSON(url, function (data) {
            $('#param_'+data.id).removeClass('danger');
            $('#param_'+data.id+' .not-deleting').removeClass('hide');
            $('#param_'+data.id+' .deleting').addClass('hide');
        });
    });
}


$('.load-form-button').click(function(){
    $('.loading').removeClass('hide');
    $('.load-form').addClass('hide');
});