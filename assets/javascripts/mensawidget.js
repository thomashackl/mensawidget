STUDIP.MensaWidget = {

    showWeek: function(week) {
        $('span.mensawidget-weekselect').removeClass('mensawidget-selected');
        $('a.mensawidget-weeklink[data-week="'+week+'"]').children('span.mensawidget-weekselect').addClass('mensawidget-selected');
        $('a.mensawidget-daylink').hide();
        $('a.mensawidget-daylink[data-week="'+week+'"]').show();
        if ($('a.mensawidget-daylink[data-week="'+week+'"][data-today="true"]').length > 0) {
            $('a.mensawidget-daylink[data-week="'+week+'"][data-today="true"]').click();
        } else {
            $('a.mensawidget-daylink[data-week="'+week+'"]').first().click();
        }
        return false;
    },

    showMenu: function(day) {
        if ($('section#mensawidget-'+day).length > 0) {
            $('section.mensawidget-menu').hide();
            $('section#mensawidget-' + day).show();
            $('span.mensawidget-dayselect').removeClass('mensawidget-selected');
            $('span#mensawidget-day' + day).addClass('mensawidget-selected');
        } else {
            // No menu for selected day -> show next available menu.
            $('span#mensawidget-day' + day).
                parent('a.mensawidget-daylink').
                next('a.mensawidget-daylink').click();
        }
        return false;
    }

};

$(function() {
    var today = new Date();
    var month = ((today.getMonth().length+1) === 1)? (today.getMonth()+1) : '0' + (today.getMonth()+1);
    STUDIP.MensaWidget.showWeek('current');
});