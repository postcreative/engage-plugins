(function() {  
    tinymce.create('tinymce.plugins.donationform', {  
        init : function(ed, url) {  
            ed.addButton('donationform', {  
                title : 'Insert Donation Form',  
                image : url + '/images/donation-form.png',  
                onclick : function() {  
					ed.focus();
                    ed.selection.setContent('[donation_form]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }  
    });  
    tinymce.PluginManager.add('donationform', tinymce.plugins.donationform);  
})(); 