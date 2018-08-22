<!DOCTYPE html>
<html>
<head>
	<title>Pratice for Vue.js</title>
</head>
<body>

	<div id="input_area">
		<h1>@{{ content }}</h1>
		<input type="text" v-model="content">
		<button v-on:click="doulbe_text">Click to make content longer</button>
		<button v-on:click="reset_text">Click to reset content</button>

		<h3 v-for="occupation in classes">
			@{{ occupation.name }}'s class color is <span :style="occupation.style">@{{ occupation.color }}</span>
		</h3>

		<a target="_blank" :href="pre_url+link_url">@{{ link_name }}</a>
		<input type="text" v-model="link_name">
		<input type="text" v-model="link_url">

		<h3>Current Xp is @{{ xp }}</h3>
		<h3>Current user type is @{{ user_type }}</h3>
		<h3 v-if="xp >= 600">You are top level now</h3>
		<h3 v-else>You are yet the top level</h3>
		<h3></h3>
		<button @click="increase_xp">Increase XP</button>
		<button @click="decrease_xp">Decrease XP</button>
		<input type="text" v-model="user_type">


		<h3>Current zip code is @{{ zip_code }}</h3>
		<h3>@{{ zip_result }}</h3>
		<input type="text" v-model="zip_code">
	</div>

	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.js"></script>
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<script type="text/javascript">
		var input_area = new Vue({  
		    el: '#input_area',
		    data: {
		    	content: "Hello",
		        link_name: "Input your link name",
		        pre_url:"http://",
		        link_url: "Input your link url",
		        xp: 0,
		        zip_code: "",
		        zip_result: "input the zip code",
		        classes: [
		        	{name: "warrior", color: "brown", style: "color: brown;"},
		        	{name: "mage", color: "blue", style: "color: blue;"},
		        	{name: "hunter", color: "green", style: "color: green;"}
		        ]
		    },
		    methods: {
		    	doulbe_text: function(){
		    		this.content += this.content;
		    	},
		    	reset_text: function(){
		    		this.content = "now we are back";
		    	},
		    	increase_xp: function(){
		    		this.xp += 100;
		    	},
		    	decrease_xp: function(){
		    		if ( this.xp-100 >= 0 ) {
		    			this.xp -=100;
		    		}
		    	},
		    	zip_request: _.debounce(function(){
		    		var self = this;
		    		this.zip_result = "searching...";
		    		axios.get('http://ziptasticapi.com/'+this.zip_code)
					.then(function (response) {
						console.log(response);
						if (response.data.error) {
							self.zip_result = response.data.error;
						} else {
							self.zip_result = response.data.city+", "+response.data.state;
						}
					})
					.catch(function (error) {
						console.log(error);
						self.zip_result = "something went wrong";
					});
		    	}, 1000)
		    },
		    computed: {
		    	user_type: {
		    		get: function(){
			    		var title = "";
			    		var user_type = "";
			    		if ( this.xp < 200 ) {
			    			title = "Entry";
			    		} else if ( this.xp < 400 ) {
			    			title = "Associate";
			    		} else if ( this.xp < 600 ) {
			    			title = "Professional";
			    		} else {
			    			title = "Expert";
			    		}
			    		user_type = title + "(" + this.xp + ")";
			    		return user_type;
			    	},
			    	set: _.debounce(function(user_type){
			    		var str = user_type.replace(")", "");
			    		var str_array =  str.split("(");
			    		this.xp = str_array[1];
			    	}, 2000)
			    }
		    },
		    watch: {
		    	zip_code: function(){
		    		this.zip_result = "inputing zip code...";
		    		if (this.zip_code.length==5) {
		    			this.zip_request();
		    		}
		    	}
		    }
		})
	</script>
</body>
</html>