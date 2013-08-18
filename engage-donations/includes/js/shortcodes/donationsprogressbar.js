(function() {  
    tinymce.create('tinymce.plugins.donationsprogressbar', {  
        init : function(ed, url) {  
            ed.addButton('donationsprogressbar', {  
                title : 'Insert Donations Progress Bar',  
                image : url + '/images/donations-progress-bar.png',  
                onclick : function() {  
					ed.focus();
                    ed.selection.setContent('[donations_progress_bar]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }  
    });  
    tinymce.PluginManager.add('donationsprogressbar', tinymce.plugins.donationsprogressbar);  
})(); 