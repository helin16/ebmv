/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	questions: {}

	,init: function(summaryListDiv, listDiv) {
		var tmp = {};
		tmp.me = this;
		tmp.i = 0;
		$H(tmp.me.questions).each(function(item) {
			$(summaryListDiv).insert({'bottom': tmp.me._getQuestionListItem('question_' + tmp.i, item.key) });
			$(listDiv).insert({'bottom': tmp.me._getQuestionDiv('question_' + tmp.i, item.key, item.value) });
			tmp.i++;
		});
	}
	
	,_getQuestionListItem: function(id, question) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class': 'questionListItem'})
			.insert({'bottom': new Element('a', {'class': 'questionItem cursorpntr'}).update(question)
				.observe('click', function() {
					Effect.ScrollTo(id, { duration:'0.2', offset:-20 });
					$(id).down('.question').click();
				})
			});
		return tmp.div;
	}
	
	,_getQuestionDiv: function(id, question, answer) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class': 'questionWrapper', 'id': id})
			.insert({'bottom': new Element('div', {'class': 'title question cursorpntr'}).update(new Element('span', {'class': 'icon'}))
				.insert({'bottom': question})
				.observe('click', function() {
					if($(this).hasClassName('shown')) {
						Effect.BlindUp($(this).up('.questionWrapper').down('.answer'), { duration: 0.5 });
						$(this).removeClassName('shown');
					} else {
						Effect.BlindDown($(this).up('.questionWrapper').down('.answer'), { duration: 0.5 });
						$(this).addClassName('shown');
					}
				})
			})
			.insert({'bottom': new Element('div', {'class': 'content answer'}).update(answer) });
		return tmp.div;
	}
});