(function() {  
    tinymce.create('tinymce.plugins.donationscollectedfunds', {  
        init : function(ed, url) {  
            ed.addButton('donationscollectedfunds', {  
                title : 'Insert Donations Collected Funds',  
                image : url + '/images/donations-collected-funds.png',  
                onclick : function() {  
					ed.focus();
                    ed.selection.setContent('[donations_collected_funds format="money"]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }  
    });  
    tinymce.PluginManager.add('donationscollectedfunds', tinymce.plugins.donationscollectedfunds);  
})(); 