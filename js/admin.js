/**************************************************************************************************/
/*
/*		File:
/*			admin.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on January 29th 2009
/*		Copyright:
/*			Copyright (c) 2009 Matthew Praetzel.
/*		License:
/*			License:
/*			This software is licensed under the terms of the GNU Lesser General Public License v3
/*			as published by the Free Software Foundation. You should have received a copy of of
/*			the GNU Lesser General Public License along with this software. In the event that you
/*			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
/*
/**************************************************************************************************/

/*-----------------------
	Initialize
-----------------------*/
(function ($) {
	
	$(document).ready(function () {
								
		//tables
		$('#members_list_fields').tableDnD({
			onDrop : function () {
				$('#fields tr:even').addClass('alternate');
				$('#fields tr:odd').removeClass('alternate');
				submit_form();
			}
		});
		
		//forms
		function submit_form() {
			var p = get_post('tern_wp_members_list_fm');
			
			$.ajax({
				async : false,
				type : 'POST',
				url : tern_wp_root+'/wp-admin/admin-ajax.php',
				dataType : 'text',
				data : p,
				success : function (m) {
					if($('#tern_members_sample_markup').get(0)) {
						$('#tern_members_sample_markup').load(tern_wp_root+'/wp-admin/admin-ajax.php','page=members-list-configure-mark-up&action=getmarkup');
					}
					$('#tern_wp_message').html(m);
				},
				error : function (m) {
					$('#tern_wp_message').html('There was an error while processing your request. Please try again.');
				}
			});
		}
		function get_post(f) {
			var f = document.getElementById(f),e = f.elements,p = '',v;
			for(var i=0;i<e.length;i++) {
				if(e[i].name) {
					if(($(e[i]).attr('type') == 'radio' || $(e[i]).attr('type') == 'checkbox') && !e[i].checked) {
						continue;
					}
					v = e[i].name + '=' + escape(e[i].value);
					p += p.length > 0 ? '&' + v : v;
				}
			}
			return p;
		}
		
		//edit fields
		$('.tern_memebrs_edit a').bind('click',function () {
			var p = $(this).parents('tr');
			edit_field(p);
			return false;
		});
		function edit_field(p) {
			p.find('.tern_members_fields').toggleClass('hidden');
			var o = p.find('.tern_memebrs_edit a');
			if(o.text() == 'Edit') {
				o.text('Quit Editing');
			}
			else {
				o.text('Edit');
			}
		}
		
		//render fields
		$('#fields .button').bind('click',function () {
			var a = ['field_titles','field_markups'];
			var p = $(this).parents('tr');
			p.find('.tern_members_fields').each(function() {
				var n = this.name ? this.name.replace('%5B%5D','') : '';
				for(k in a) {
					if(this.name && n == a[k]) {
						p.find('.'+n).text(this.value);
						break;
					}
				}
			});
			submit_form();
			edit_field(p);
		});
								
	});
								
})(jQuery);