(function() {  
    tinymce.create('tinymce.plugins.donationslist', {  
        init : function(ed, url) {  
            ed.addButton('donationslist', {  
                title : 'Insert Donations List',  
                image : url + '/images/donations-list.png',  
                onclick : function() {  
					ed.focus();
                    ed.selection.setContent('[donations_list]');  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        }  
    });  
    tinymce.PluginManager.add('donationslist', tinymce.plugins.donationslist);  
})(); 