(function() {  
    tinymce.create('tinymce.plugins.donationstarget', {  
        init : function(ed, url) {  
            ed.addButton('donationstarget', {  
                title : 'Insert Donations Target',  
                image : url + '/images/donations-target.png',  
                onclick : function() {  
					ed.focus();
                    ed.selection.setContent('[donations_target format="money"]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }  
    });  
    tinymce.PluginManager.add('donationstarget', tinymce.plugins.donationstarget);  
})(); 