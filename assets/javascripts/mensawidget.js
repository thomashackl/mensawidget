STUDIP.MensaWidget = {

    showWeek: function(week) {
        $('a.mensawidget-daylink').hide();
        $('span.mensawidget-weekselect').removeClass('mensawidget-selected');
        $('a.mensawidget-weeklink[data-week="'+week+'"]').children('span.mensawidget-weekselect').addClass('mensawidget-selected');
        $('a.mensawidget-daylink[data-week="'+week+'"]').fadeIn(500);
        if ($('a.mensawidget-daylink[data-week="'+week+'"][data-today="true"]').length > 0) {
            var today = new Date();
            var thisday = ((today.getDate() >= 10)? (today.getDate()) : ('0' + today.getDate()));
            var month = ((today.getMonth().length+1) === 1)? (today.getMonth()+1) : '0' + (today.getMonth()+1);
            if (today.getHours() < 14) {
                $('a.mensawidget-daylink[data-week="' + week + '"][data-today="true"]').click();
            } else {
                $('a.mensawidget-daylink[data-week="' + week + '"][data-today="true"]').next().click();
            }
        } else {
            if (week == 'current') {
                $('a.mensawidget-daylink[data-week="'+week+'"]').last().click();
            } else {
                $('a.mensawidget-daylink[data-week="'+week+'"]').first().click();
            }
        }
        return false;
    },

    showMenu: function(day) {
        // If selected day has a menu, show it.
        if ($('section#mensawidget-'+day).length > 0) {
            $('section.mensawidget-menu').hide();
            $('section#mensawidget-' + day).fadeIn(500);
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
    STUDIP.MensaWidget.showWeek('current');
});