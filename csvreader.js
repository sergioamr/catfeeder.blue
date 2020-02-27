
// Set the dimensions of the canvas / graph
var	margin = {top: 10, right: 20, bottom: 20, left: 25},
	width = 620 - margin.left - margin.right,
	height = 160 - margin.top - margin.bottom;

// Parse the date / time
var	parseDate = d3.time.format("%Y-%m-%d %H:%M").parse;

// Set the ranges
var	x = d3.time.scale().range([0, width]);
var	y = d3.scale.linear().range([height, 0]);

// Define the axes
var	xAxis = d3.svg.axis().scale(x)
	.orient("bottom").ticks(5);

var	yAxis = d3.svg.axis().scale(y)
	.orient("left").ticks(5);

// Define the line
var	valueline = d3.svg.line()
	.x(function(d) { return x(d.date); })
	.y(function(d) { return y(d.close); });
    
// Adds the svg canvas
var	chart1 = d3.select("#area1")
	.append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
	.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// Get the data
d3.csv("data-1.csv?random="+Math.random(), function(error, data) {
	prev = 0;
	var avg = 0;
	
	data.forEach(function(d) {
		d.date = parseDate(d.date);
		number = parseInt(d.close);

		//console.log("Data "+parseInt(d.close) + " " + avg);
		if (number>50) {
			d.close = avg;
		} else 
		if (prev!=0 && Math.abs(number-avg)>5 ) {

			d.close = avg;
			avg = (10*avg + number)/11; 

			prev = d.close;
		} else {
			d.close = parseInt(d.close);
			prev = d.close;
			avg = (2*avg + parseInt(d.close))/3;
		}
	});

	// Scale the range of the data
	x.domain(d3.extent(data, function(d) { return d.date; }));
	y.domain([0, d3.max(data, function(d) { return d.close; })]);

	// Add the valueline path.
	chart1.append("path")
		.attr("class", "line")
		.attr("d", valueline(data));

	// Add the X Axis
	chart1.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);

	// Add the Y Axis
	chart1.append("g")
		.attr("class", "y axis")
		.call(yAxis);

});

var food_today_grams = 0;

d3.csv("data-2.csv?random="+Math.random(), function(error, data) {
       data.forEach(function(d) {
                d.date = parseDate(d.date);
                d.close = parseInt(d.close)

		var dt = new Date();
		if (dt.getDay() == d.date.getDay() && dt.getMonth()== d.date.getMonth())
			food_today_grams += d.close; 
        });

	document.getElementById('food_today').innerHTML = "Total today "+ food_today_grams+ " grams";
/*
        // Scale the range of the data
        x.domain(d3.extent(data, function(d) { return d.date; }));
        y.domain([0, d3.max(data, function(d) { return d.close; })]);

        // Add the valueline path.
        chart1.append("path")
                .attr("class", "line")
                .attr("d", valueline(data));

        // Add the X Axis
        chart1.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + height + ")")
                .call(xAxis);

        // Add the Y Axis
        chart1.append("g")
                .attr("class", "y axis")
                .call(yAxis);
*/
});
