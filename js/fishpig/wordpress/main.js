

if (!window.fishpig) {
	var fishpig = {};
}

fishpig.WordPress = {};
	
fishpig.WordPress.searchForm = Class.create(Varien.searchForm, {
	initialize : function($super, form, field, emptyText, useSeoUrls) {
		this.useSeoUrls = useSeoUrls;
		return $super(form, field, emptyText);
	},
    submit : function($super, event) {
    	if (this.useSeoUrls) {
			var o = this.form.readAttribute('action');
			var n = this.form.readAttribute('action') + encodeURIComponent(this.field.getValue().replace(' ', '-')) + '/';
	
			this.form.writeAttribute('action', n);
			this.field.writeAttribute('disabled', 'disabled');
			
			if (!$super(event)) {
				this.form.writeAttribute('action', o);
				this.field.writeAttribute('disabled', null);
				
				Event.stop(event);
				return false;
			}
			
			return true;
		}

		return $super(event);
    }
});

