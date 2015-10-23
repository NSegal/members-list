/**************************************************************************************************/
/***
/***	ternstyle (TM) FORM VALIDATION JAVASCRIPT DOCUMENT (ternForm (tm) version 3.1 with jQuery)
/***	-----------------------------------------------------------------------
/***	Written by Matthew Praetzel. Copyright (c) 2007 Matthew Praetzel.
/***	-----------------------------------------------------------------------
/***	All Rights Reserved. Any use of these functions & scripts without written consent is prohibited.
/***
/**************************************************************************************************/

/*-----------------------
	ternForm (tm) v3.1
-----------------------*/
var ternForm = function (i) {
	var a = _ternform.instances,l = a.length;
	if(window == this) {
		v = _ternform.inArray(a,i,'node');
		if(v !== false) {
			return a[v]['object'];
		}
		a[l] = new Array();
		a[l]['node'] = i;
		a[l]['object'] = new ternForm(i);
		return a[l]['object'];
	}
	this.name = i
	this.tern = jQuery(i).get(0);
};
ternForm.prototype = _ternform = {
	instances : [],
	initFields : {
		allow_one_checked : {
			types : ['checkbox']
		}
	},
	fields : {
		required : {
			label : 'The following fields are required:',
			types : ['text','password','textarea','select-one','select-multiple','checkbox','radio'],
			errors : ''
		},
		onehasvalue : {
			label : 'You must assign a value to one of the following:',
			types : ['text','file','password','textarea','select-one','select-multiple','checkbox','radio'],
			errors : ''
		},
		alpha : {
			label : 'The following fields must contain only letters:',
			types : ['text'],
			errors : ''
		},
		alphanum : {
			label : 'The following fields must be alphanumeric:',
			types : ['text'],
			errors : ''
		},
		numeric : {
			label : 'The following fields must be numeric:',
			types : ['text'],
			errors : ''
		},
		confirm : {
			label : 'The following fields do not match:',
			types : ['text','password'],
			errors : ''
		},
		email : {
			label : 'These email addresses do not seem to be properly formatted:',
			types : ['text'],
			errors : ''
		},
		length : {
			label : 'The following fields are not the correct length:',
			types : ['text','password','textarea'],
			errors : ''
		},
		agreement : {
			label : '',
			types : ['checkbox'],
			errors : ''
		},
		onechecked : {
			label : '',
			types : ['checkbox','radio'],
			errors : ''
		},
		onecheckedreg : {
			label : '',
			types : ['checkbox','radio'],
			errors : ''
		},
		captcha : {
			label : 'You did not answer the following questions correctly:',
			types : ['text','password','textarea','select-one'],
			errors : ''
		}
	},
	divs : { ':' : 'is','=' : 'equal to','>' : 'greater than','<' : 'less than','>=' : 'greater than or equal to','<=' : 'less than or equal to' },
	errors : [],
	oneofs : [],
	
	//parameters
	css : 'required',
	func : null,
	efunc : null,
	submit : true,
	//end parameters
	
	init :
	function (a) {
		var th = this;
		if(!this.tern) { return; }
		for(k in a) {
			this[k] = a[k];	
		}
		jQuery(th.name).bind('submit',function () { return false; });
		jQuery(th.name).bind('submit',function () { th.validateAndHandle(); });
		th.er = jQuery(th.name+' div.errors_cn').get(0) ? jQuery(th.name+' div.errors_cn') : jQuery(th.name).prepend('<div class="errors_cn"><div class="errors"></div></div>').find('.errors_cn');
		if(th.er) {
			th.ec = jQuery(th.name+' div.errors_cn div.errors');
		}
		th.initForm();
		return th;
	},
	initForm :
	function () {
		var a = this.tern.elements;
		for(var i=0;i<a.length;i++) {
			var p = this.getParams(a[i]);
			if(p) {
				for(k in p) {
					if(this.initFields[k] && this.inArray(this.initFields[k].types,this.inputType(a[i])) !== false) {
						eval('this.'+k+'(a[i]);');
					}
				}
			}
		}
	},
	validateAndHandle :
	function(c,f,e) {
		var th = this;
		th.unlabel(th.css);
		this.validate();
		if(this.errors.length > 0) {
			if(th.er) {
				th.ec.animate({
					opacity : 0
				},'fast','linear',function () { return th.error(); });
			}
			return false;
		}
		else {
			th.resetErrorDiv();
			th.fixMultipleSelects();
			if(th.func) { th.func(th.tern); }
			this.post();
		}
		return true;
	},
	validate :
	function () {
		var a = this.tern.elements;
		for(var i=0;i<a.length;i++) {
			if(a[i].nodeName.toLowerCase() != 'fieldset') {
				var p = this.getParams(a[i]);
				if(p) {
					for(k in p) {
						if(this.fields[k] && this.inArray(this.fields[k].types,this.inputType(a[i])) !== false) {
							eval('this.'+k+'(a[i],p[k][0],p[k][1]);');
						}
					}
				}
			}
		}
	},
	required :
	function (i) {
		if(!this.inputValue(i)) {
			this.addErrors('required',i,'<li>' + this.getTitle(i) + '</li>');
		}
	},
	onehasvalue :
	function (i,n) {
		var a = this.tern.elements,s='',c=[];
		if(!this.inputValue(i) && !this.oneofs[n]) {
			s = this.getTitle(i);
			c[c.length] = i;
			for(var b=0;b<a.length;b++) {
				var p = this.getParams(a[b]);
				if(p && p['onehasvalue'] && n == p['onehasvalue'][0] && this.inputValue(a[b])) {
					return true;
				}
				else if(p && p['onehasvalue'] && n == p['onehasvalue'][0] && a[b] != i) {
					c[c.length] = a[b];
					s += ' or '+this.getTitle(a[b]);
				}
			}
			for(k in c) {
				this.errors[this.errors.length] = c[k];
			}
			this.oneofs[n] = true;
			this.addErrors('onehasvalue',i,'<li>Please fill out: '+s+'</li>');
		}
	},
	captcha :
	function (i,v) {
		if(this.inputValue(i) != v) {
			this.addErrors('captcha',i,'<li>' + this.getTitle(i) + '</li>');
		}
	},
	alpha :
	function (i) {
		var r = /^[a-zA-Z_-]+$/g;
		if(this.inputValue(i).length > 0 && !r.test(this.inputValue(i))) {
			this.addErrors('alpha',i,'<li>' + this.getTitle(i) + '</li>');
		}
	},
	alphanum :
	function (i) {
		var r = new RegExp('^[0-9a-zA-Z_-]+$','g'),t = r.test(this.inputValue(i));
		if(this.inputValue(i).length > 0 && !t) {
			this.addErrors('alphanum',i,'<li>' + this.getTitle(i) + '</li>');
		}
	},
	numeric :
	function (i) {
		var r = /^[0-9]+$/g;
		if(this.inputValue(i).length > 0 && !r.test(this.inputValue(i))) {
			this.addErrors('numeric',i,'<li>' + this.getTitle(i) + '</li>');
		}
	},
	confirm :
	function (i,v) {
		var a = this.tern.elements;
		for(var b=0;b<a.length;b++) {
			if(a[b].name == v && this.inputValue(a[b]) != this.inputValue(i)) {
				this.addErrors('confirm',i,'<li>' + this.getTitle(i) + '</li>');
			}
		}
	},
	email : 
	function (i) {
		var r = new RegExp('[a-zA-Z0-9.-_+]+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$','g'),v = this.inputValue(i);
		if(v && v.length && !r.test(v)) {
			this.addErrors('email',i,'<li>' + this.getTitle(i) + '</li>');
		}
	},
	length :
	function (i,v,d) {
		if(!eval('this.inputValue(i).length '+d+' v')) {
			var e = this.divs[d];
			this.addErrors('length',i,'<li>' + this.getTitle(i) + ' - must be '+e+' '+v+' characters long.</li>');
		}
	},
	agreement :
	function (i) {
		if(!this.inputValue(i)) {
			this.addErrors('agreement',i,'<li>You must agree to the terms of use.</li>');
		}
	},
	onechecked :
	function (i) {
		var a = this.tern.elements;
		if(!i.checked) {
			for(var b=0;b<a.length;b++) {
				if(a[b].name == i.name && a[b].checked) {
					return true;
				}
			}
			this.addErrors('onechecked',i,'<li>Please select one for: '+this.getTitle(i)+'</li>');
		}
	},
	onecheckedreg :
	function (i,v) {
		var a = this.tern.elements;
		if(!i.checked) {
			for(var b=0;b<a.length;b++) {
				if((new RegExp(v,'g')).test(a[b].name) && a[b].checked) {
					return true;
				}
			}
			this.addErrors('onecheckedreg',i,'<li>Please select one for: '+this.getTitle(i)+'</li>');
		}
	},
	allow_one_checked :
	function (i) {
		var th = this,a = this.tern.elements;
		for(var b=0;b<a.length;b++) {
			if(a[b].name == i.name) {
				ternEvents.addEvent(a[b],function (e) {
					i.checked = true;
					th.uncheck(e,i.name);
				},"click",false);
			}
		}
	}
};
ternForm.prototype.extend = 
function (a,i) {
	for(k in a) {
		this[k] = _ternform[k] = a[k];
	}
};
ternForm.prototype.extend({
	addErrors :
	function (t,i,m) {
		this.fields[t].errors += m;
		this.errors[this.errors.length] = i;
	},
	error :
	function (g) {
		this.label(this.css);
		this.showErrors(g);
		this.resetErrors();
	},
	compileErrors :
	function () {
		var f = this.fields,e='';
		for(k in f) {
			if(f[k].errors.length) {
				e += f[k].label.length ? '<ul><li><b>' + f[k].label + '</b><ul>' + f[k].errors + '</ul></li></ul>' : '<ul><li><b>' + f[k].errors + '</b></ul>';
			}
		}
		return e;
	},
	showErrors :
	function (g) {
		var th = this,f = th.efunc ? th.efunc : function(){};
		th.ec.html(th.compileErrors());
		if(th.er) {
			th.er.css('visibility','visible').animate({
				'height' : th.ec.innerHeight()
			},'fast','linear',function () { th.ec.animate({ opacity : 1 },'fast','linear',f); });
		}
		this.errorsOn = true;
	},
	label :
	function (c,a) {
		var a = a ? a : this.errors;
		for(k in a) {
			if(a[k]) {
				var l = this.tern.getElementsByTagName('label');
				for(var i=0;i<l.length;i++) {
					if(l[i].htmlFor != null && l[i].htmlFor == a[k].name) {
						jQuery(l[i]).addClass(c);
						break;
					}
				}
			}
		}
	},
	resetErrorDiv :
	function() {
		if(this.errorsOn) {
			if(this.er) {
				this.ec.animate({
					opacity : 0
				},'fast','linear');
			}
			this.er.animate({
				'height' : 0
			},'fast','linear').css('visibility','hidden');
			this.errorsOn = false;
		}
		return this;
	},
	resetErrors :
	function () {
		var f = this.fields;
		for(k in f) {
			f[k].errors = '';
		}
		this.errors = [];
		return this;
	},
	unlabel :
	function (c) {
		var ls = this.tern.getElementsByTagName("label");
		for(var i=0;i<ls.length;i++) {
			jQuery(ls[i]).removeClass(c);
		}
	}
});
ternForm.prototype.extend({
	initHover :
	function (h,a) {
		var th = this,i = th.tern.elements,l = th.tern.getElementsByTagName("li");
		for(var b=0;b<i.length;b++) {
			i[b].onfocus = function (e) {
				var s = this;
				while(s.nodeName.toLowerCase() != "li") {
					if(s != th.tern) {
						s = s.parentNode;
					}
					else {
						break;
					}
				}
				ternStyle(l).removeClass(a);
				if(s != th.tern) {
					ternStyle(s).addClass(a);
				}
			}
			i[b].onblur = function () {
				ternStyle(l).removeClass(a);
			}
		}
		ternStyle(l).hovers(h);
		return th;
	}
});
ternForm.prototype.extend({
	post : 
	function (f) {
		if(this.submit) {
			var f = f ? f : this.tern;
			f.submit();
		}
	},
	getTitle :
	function (f) {
		var i = f.title.indexOf("::");
		return i ? f.title.substr(0,i) : f.title;
	},
	getParams :
	function (i) {
		var a=[],t = i.title.toLowerCase(),k,d,q,v;
		if(t.indexOf('::') == -1) {
			return false;
		}
		else {
			t = t.substr(t.indexOf('::')+3);
			t = t.split(' ');
			for(var b=0;b<t.length;b++) {
				k = d = q = v = null;
				for(k in this.divs) {
					if(t[b].indexOf(k) != -1) {
						d = k;
						q = t[b].split(k);
						break;
					}
				}
				k = q ? q[0] : t[b],v = q ? q[1] : '';
				a[k] = [v,d];
			}
		}
		return a;
	},
	inputValue :
	function (i,b) {
		var i = i ? jQuery(i).get(0) : this.tern,t = this.inputType(i),a=new Array();
		if(t == 'text' || t == 'file' || t == 'password' || t == 'textarea' || t == 'submit' || t == 'reset' || t == 'hidden' || t == 'image' || t == 'button') {
			return i.value;
		}
		else if(t == 'select-one') {
			return i.options[i.selectedIndex].value;
		}
		else if(t == 'select-multiple') {
			for(var b=0;b<i.options.length;b++) {
				if(i.options[b].selected) {
					a.push(i.options[b].value);
				}
			}
			return a;
		}
		else if((t == 'checkbox' || t == 'radio') && (!b || (b && i.checked))) {
			return b ? i.value : i.checked;
		}
	},
	inputType :
	function (i) {
		var i = i ? i : this.tern;
		if(i && i.type) { return i.type; }
		return '';
	},
	getFromPost :
	function (f) {
		var f = f ? jQuery(f).get(0) : this.tern,e = f.elements,p = '',v;
		for(var i=0;i<e.length;i++) {
			if(e[i].name) {
				if((this.inputType(e[i]) == 'radio' || this.inputType(e[i]) == 'checkbox') && !e[i].checked) {
					continue;
				}
				v = e[i].name + '=' + e[i].value;
				p += p.length > 0 ? '&' + v : v;
			}
		}
		return p;
	},
	fixMultipleSelects :
	function(s,f) {
		var f = f ? jQuery(f).get(0) : this.tern,e = f.elements,o,p='',s = s ? s : ',',t;
		for(var i=0;i<e.length;i++) {
			if(this.inputType(e[i]) == 'select-multiple') {
				o = e[i].options;
				for(var a=0;a<o.length;a++) {
					if(o[a].selected == true) {
						p += p.length > 0 ? s + o[a].value : o[a].value;
					}
				}
				t = this.createTextInput(e[i].name,p);
				e[i].parentNode.replaceChild(t,e[i]);
				i--;
			}
		}
	},
	multiIsSelected :
	function (i) {
		var i = i != undefined ? i : this.tern;
		var o = i.options;
		var ops = '';
		for(var a=0;a<o.length;a++) {
			if(o[a].selected == true) {
				return true;
			}
		}
		return false;
	},
	createTextInput :
	function (n,v,s,c) {
		var i = this.setAttr(document.createElement('input'),{'tupe':'text','name':n,'id':n,'size':s});
		i.value = v;
		if(c) { jQuery(i).addClass(c); }
		return i;
	},
	createTextarea :
	function (n,v,c) {
		var i = this.setAttr(document.createElement('textarea'),{'name':n,'id':n});
		i.value = v;
		if(c) { jQuery(i).addClass(c); }
		return i;
	},
	createHidden :
	function(n,v) {
		var i = this.setAttr(document.createElement('input'),{'type':'hidden','name':n,'id':n});
		i.value = v;
		return i;
	},
	addHiddenToForm :
	function (n,v) {
		var f = this.tern,i = this.createHidden(n,v);
		f.appendChild(i);
	},
	setAttr :
	function (i,a) {
		for(k in a) {
			i.setAttribute(k,a[k]);
		}
		return i;
	}
});
ternForm.prototype.extend({
	allowOneChecked :
	function(n) {
		var th = this,f = th.tern,e = f.elements;
		for(var i=0;i<e.length;i++) {
			if(e[i].name == n) {
				jQuery(e[i]).bind('click',[v,n],function (e,v) { th.uncheck(e,v); });
			}
		}
		return th;
	},
	uncheck :
	function (s,v) {
		var f = this.tern,e = f.elements,s = this.eventSource(s);
		for(var i=0;i<e.length;i++) {
			if(e[i].name == n && e[i] != s) {
				e[i].checked = false;
			}
		}
	}
});
ternForm.prototype.extend({
	select :
	function (n) {
		return this.setAttr(document.createElement('select'),{'name':n,'id':n});
	},
	selectOption :
	function (v,n) {
		var o = document.createElement('option');
		o.value = v;
		o.text = n;
		return o;
	},
	addSelectOption :
	function (f) {
		var o = document.createElement('option');
		o.text = 'Select';
		o.value = '';
		f.options[0] = o;
	},
	addOption :
	function (f,o) {
		f.options[f.options.length] = o;
	},
	selectFn :
	function (f,fn) {
		jQuery(f).bind('change',fn);
	}
});
ternForm.prototype.extend({
	eventSource :
	function (e) {
		var i,e = e ? e : ternEvents.setEvent(e);
		i = e.srcElement ? e.srcElement : e.target;
		if(i.nodeType == 3) {
			i = i.parentNode;
		}
		return i;
	},
	inArray :
	function (a,v,t) {
		for(var i=0;i<a.length;i++) {
			if(a[i] === v && !t) {
				return i;
			}
			else if(a[i][t] === v) {
				return i;
			}
		}
		return false;
	}
});