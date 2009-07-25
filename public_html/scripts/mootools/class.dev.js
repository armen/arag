// Mootools Class

/*
Script: Class.Refactor.js
	Extends a class onto itself with new property, preserving any items attached to the class's namespace.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

Class.refactor = function(original, refactors){

	$each(refactors, function(item, name){
		var origin = original.prototype[name];
		if (origin && (origin = origin._origin) && typeof item == 'function') original.implement(name, function(){
			var old = this.previous;
			this.previous = origin;
			var value = item.apply(this, arguments);
			this.previous = old;
			return value;
		}); else original.implement(name, item);
	});

	return original;

};

/*
Script: Class.Binds.js
	Automagically binds specified methods in a class to the instance of the class.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

Class.Mutators.Binds = function(binds){
    return binds;
};

Class.Mutators.initialize = function(initialize){
	return function(){
		$splat(this.Binds).each(function(name){
			var original = this[name];
			if (original) this[name] = original.bind(this);
		}, this);
		return initialize.apply(this, arguments);
	};
};

/*
Script: Class.Occlude.js
	Prevents a class from being applied to a DOM element twice.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

Class.Occlude = new Class({

	occlude: function(property, element){
		element = document.id(element || this.element);
		var instance = element.retrieve(property || this.property);
		if (instance && !$defined(this.occluded)){
			this.occluded = instance;
		} else {
			this.occluded = false;
			element.store(property || this.property, this);
		}
		return this.occluded;
	}

});

/*
Script: Chain.Wait.js
	Adds a method to inject pauses between chained events.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

(function(){

	var wait = {
		wait: function(duration){
			return this.chain(function(){
				this.callChain.delay($pick(duration, 500), this);
			}.bind(this));
		}
	};

	Chain.implement(wait);

	if (window.Fx){
		Fx.implement(wait);
		['Css', 'Tween', 'Elements'].each(function(cls){
			if (Fx[cls]) Fx[cls].implement(wait);
		});
	}

	try {
		Element.implement({
			chains: function(effects){
				$splat($pick(effects, ['tween', 'morph', 'reveal'])).each(function(effect){
					effect = this.get(effect);
					if (!effect) return;
					effect.setOptions({
						link:'chain'
					});
				}, this);
				return this;
			},
			pauseFx: function(duration, effect){
				this.chains(effect).get($pick(effect, 'tween')).wait(duration);
				return this;
			}
		});
	} catch(e){}

})();
