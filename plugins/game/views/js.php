<script>
<?php
	//TODO: preloader
	//TODO: protocol ehm..
	//TODO: allow multipue maps on 1 page
	//TODO: rightclick menu
	//TODO: texture slots
	//TODO: co-op editing
	//TODO: 3d (not important (for now))
	?>
	jQuery(function($)
	{
		$.log = {};
		$.log.DEBUG = 0;
		$.log.INFO = 1;
		$.log.NOTICE = 2;
		$.log.WARNING = 3;
		$.log.FATAL = 4;
		var loglevel = 0;
		$.log.write_log = function (level, message)
		{
			if(level < loglevel) return false;
			$('#log').html(	$('#log').html()+'<div data-log-level="'+level+'" >'+message+'</div>');
			console.log('('+level+') '+message);
			
			if (level == $.fn.LOG_FATAL)
				throw new exeption('fatal error: '+message);
			
			return $('#log').html();
		};
		
		$.log.set_log_level = function (level)
		{
			loglevel = level;		
		};
		var map_hash = $('.map .hash').html();
		$.log.write_log($.log.INFO, 'init map (hash='+map_hash+')');
		var width = 25;
		var height = 25;
		
		var res_x = 15;
		//var res_y = 15;//unused
		
		var root = $('.map');
		root.attr('data-loaded', '0');
		

		$('.map div').each(function(index, element){
//			var me = $(this);
//			console.log(me);
			var x = $(element).attr('data-map-x');
			var y = $(element).attr('data-map-y');
			
			if (y ==0) $(element).css('clear', 'left');
			
			$(element).click(function()
			{
				$.log.write_log($.log.DEBUG, "click on "+x+";"+y);
								
				$.get('ajax/click/'+x+'/'+y, function(data) {
					$.log.write_log($.log.DEBUG, "ajax: "+data+' {error='+data.error+', result='+data.result+' hash='+data.hash+'}');
					for (var i = 0; i < data.result.length; i++)
					{
						var item = data.result[i];
						$.log.write_log($.log.DEBUG, 'action: '+item[0]+' x: '+item[1]+' y: '+item[2]+' arg1: '+item[3]);
						
						switch (item[0])
						{
							case 'change':
								$.log.write_log($.log.DEBUG, 'Changing: '+'[data-map-x='+item[1]+'][data-map-y='+item[2]+'] \'s src to '+item[3].img);
								$.log.write_log($.log.DEBUG, $('[data-map-x='+item[1]+'][data-map-y='+item[2]+'] img'))
								$('[data-map-x='+item[1]+'][data-map-y='+item[2]+'] img').attr('src', item[3].img);
								break;
						
						}
						
						//map changed?
						//TODO:non full reload (co-op mapchanging =D)
						if (item[0] != 'change' && map_hash != data.hash)
							document.location = '.';

					}
				}, 'json');
			})
		});
		
		//TODO:hash checkserum
		var JS_protocol = {
			stack: [],
			bindings: {},
			
			add_message : function(message)
			{
				$.log.write_log($.log.DEBUG, "message "+message['name']+" ("+message.arguments+")");
				this.stack[this.stack.length] = message;
			},
			
			add_simple_message : function(name, arguments)
			{
				$.log.write_log($.log.DEBUG, "message "+name+" ("+arguments+")");
				this.stack[this.stack.length] = { name: name, arguments: arguments, arg_hash: ''}
			},
			
			update: function()
			{
				$.log.write_log($.log.DEBUG, "updating ("+this.stack+")");
				$.post('ajax_v2/ajax', {msg: this.stack}, function(data)
				{
					$.log.write_log($.log.DEBUG, data);
					for (var i = 0; i < data.length; i++)
						JS_protocol.call_binding(data[i]['name'], data[i].arguments);
					
					JS_protocol.stack = [];
				}, 'json');
			
			},
			
			bind: function (name, func)
			{
				$.log.write_log($.log.DEBUG, "binding: "+name+" ("+func+")");
				this.bindings[name] = func
			},
			
			call_binding: function (name, arguments)
			{
				if (this.bindings[name] == undefined)
					$.error('could not find binding: '+name);
				else
					this.bindings[name].apply({}, arguments);
			},
			
			a: function(){ console.log(this); }
			
		}
		
/*		JS_protocol.a();
		JS_protocol.add_simple_message('yay', {'name' : 'arg1!', 'name2' : 'arg2!'});
		JS_protocol.add_simple_message('yay', {'name' : 'arg1!', 'name2' : 'arg2!'});
		JS_protocol.add_simple_message('yay', {'name' : 'arg1!', 'name2' : 'arg2!'});
		JS_protocol.add_simple_message('yay', {'name' : 'arg1!', 'name2' : 'arg2!'});
		JS_protocol.add_simple_message('yay', {'name' : 'arg1!', 'name2' : 'arg2!'});
		JS_protocol.update();
		JS_protocol.a();*/
		
		var texture = {
			url:'',
//			modifications = {} //future
			preload : function()
			{
				//TODO
			},
		}
		
		var block = {
			texture_id: null,
			name: "",
//			collision, //future
			element : null,
			events : {}
		}
		
		var map_part = {
			block: {},
			events : {},
			
			override_collision: -1,
			
			render: function(x, y){
				var element = AJAX_map.element.find('[data-map-x='+x+'][data-map-y='+x+']')
				
				if (element.attr('title') != this.block['name'])
					element.html('<div data-map-x='+ x +' data-map-y='+ y +'><img src="'+ AJAX_map.textures[this.block.texture].url +'" title="'+ this.block['name'] +'" alt="'+ this.block['name'] +"\n");
				else
					return true;
				
				return false;
			
			}
		}
		
		var AJAX_map = {
			blocks: [], //material types
			map: [], //map blocks (map_parts)
			element: null,
			events : {},
			
			textures : [],
			
			load_texture : function (texture, id)
			{
				if (id == null)
					id = AJAX_map.textures.length + 1000;//first 1000 are reserved

				AJAX_map.textures[id] = texture;
				return id
			},
			
			init_scripts : function()
			{
				JS_protocol.bind('script_reload', function(script)
				{
					eval(script);
				});
				JS_protocol.add_simple_message('reload', {'type' : 'script'});
			},
			
			init_textures : function ()
			{
				JS_protocol.bind('texture_reload', function(texture_map)
				{
					for (var i = 0; i < texture_map.length; i++)
					{
						texture_map[i].preload();
						AJAX_map.load_texture()
					}
				});
				
				JS_protocol.add_simple_message('reload', {'type' : 'textures'});
			},
			
			init_blocks: function()
			{
				JS_protocol.bind('block_reload', function(blocks)
				{
					for (var i = 0; i < blocks.length; i++)
					{
						AJAX_map.blocks = []; //clear row
						
						var texture
						if (blocks[i].texture != undefined)
						{
							//instant adding textures (ugly)
							texture = AJAX_map.load_texture(blocks[i].texture);
						}
						else if (blocks[i].texture == undefined && blocks[i].texture_id == undefined)
							//no texture, UGLY!
							texture = -1;
						else
							//texture id => nice!
							texture = blocks[i].texture_id;
						
						var newblock = new block();
						newblock.texture_id = texture;
						AJAX_map.blocks[i] = newblock;				
					}
				});
				
				JS_protocol.add_simple_message('reload', {'type' : 'block'});		
			
			},
			
			init_map : function ()
			{
				JS_protocol.bind('map_reload', function(map)
				{
					for (var x = 0; x < map.length; x++)
					{
						AJAX_map.map[x] = []; //clear row
						for (var y = 0; y < map[x].length; y++)
						{
							var texture
							if (map[x][y].texture != undefined)
							{
								AJAX_map.load_texture(map[x][y].texture);
							}
							else if (map[x][y].texture == undefined && map[x][y].texture_id == undefined)
								texture = 'DEFAULT';
							else
								texture = AJAX_map.textures[map[x][y].texture_id];
							
							AJAX_map.map[x][y]	
							AJAX_map.element.append('<div data-map-x='+ x +' data-map-y='+ y +'><img src="'+ texture +'" title="'+ map[x][y]['name'] +'" alt="'+ map[x][y]['name'] +"\n");
						
						
						}
					}
				});
				
				JS_protocol.add_simple_message('reload', {'type' : 'map'});
			},
			
			init_events: function ()
			{
				JS_protocol.bind('events_reload', function(events)
				{
					for (var i = 0; i < events.length; i++)
					{
						AJAX_map.element.children().off(events[i]['name']);
						AJAX_map.element.children().on(events[i]['name'], function(event){
							eval(events[i]['function']);
						});
					}
				});
				
				JS_protocol.bind('events_clear', function()
				{
					AJAX_map.element.children().off(events[i]['name']);
				});
				
				JS_protocol.add_simple_message('reload', {'type' : 'events'});
			},
			
			init: function()
			{
				this.init_scripts();
				this.init_textures();
			
				JS_protocol.update();
			}
		
		};
		
		$.fn.map_event = function (event, arguments)
		{
			var element = $(this);
			var x = $(element).attr('data-map-x');
			var y = $(element).attr('data-map-y');

			var block = AJAX_map.find_block(x, y);
			
			if(block.events[event] != undefined)
				return block.events[event].apply(element, arguments);
			else if(AJAX_map.events[event] != undefined)
				return AJAX_map.events[event].apply(element, arguments);
			else
				$.error('could not find event');
		}

		AJAX_map.init();
	})//(jQuery)
</script>
