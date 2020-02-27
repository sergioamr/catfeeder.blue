function create_graph(div_name, file_name, id_total) {
    var	margin = {top: 10, right: 20, bottom: 20, left: 25},
	width = 860 - margin.left - margin.right,
    height = 200 - margin.top - margin.bottom;

    var svg = d3.select(div_name).append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        ,
        margin = {top: 20, right: 80, bottom: 30, left: 50},
        width = svg.attr("width") - margin.left - margin.right,
        height = svg.attr("height") - margin.top - margin.bottom,
    g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var parseTime = d3.timeParse("%Y%m%d");
    var	parseTime = d3.timeParse("%Y-%m-%d %H:%M");
    //var parseTime = d3.time.format("%Y-%m-%d %H:%M").parse;

    var x = d3.scaleTime().range([0, width]),
        y = d3.scaleLinear().range([height, 0]),
        z = d3.scaleOrdinal(d3.schemeCategory10);

    var line = d3.line()
        .curve(d3.curveBasis)
        .x(function (d) { return x(d.date); })
        .y(function (d) { return y(d.weight); });

    d3.csv(file_name, type, function (error, data) {
        if (error) throw error;

        var food = data.columns.slice(1).map(function (id) {
            return {
                id: id,
                values: data.map(function (d) {
                    if (d[id] > 120)
                        d[id] = 120;

                    return {
                        date: d.date,
                        weight: d[id]
                    };
                })
            };
        });

        var total = d3.select(id_total);
        len = food[0].values.length-1;

        weight = food[0].values[len].weight;
        total_weight = food[1].values[len].weight;

        var consumption = total_weight - weight;
        total.html("&nbsp;Consumed " + consumption + " grams");

        x.domain(d3.extent(data, function (d) { return d.date; }));

        y.domain([
            d3.min(food, function (c) { return d3.min(c.values, function (d) { return d.weight; }); }),
            d3.max(food, function (c) { return d3.max(c.values, function (d) { return d.weight; }); })
        ]);

        z.domain(food.map(function (c) { return c.id; }));

        g.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + height + ")")
            .attr("fill", "#FFF")
            .call(d3.axisBottom(x));

        g.append("g")
            .attr("class", "axis axis--y")
            .call(d3.axisLeft(y))
            .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", "0.71em")
            .attr("fill", "#EEE")
            .text("weight, g");

        var city = g.selectAll(".city")
            .data(food)
            .enter().append("g")
            .attr("fill", "#FFF")
            .attr("class", "city");

        city.append("path")
            .attr("class", "line")
            .attr("fill", "#FFF")
            .attr("d", function (d) { return line(d.values); })
            .style("stroke", function (d) { return z(d.id); });

        city.append("text")
            .datum(function (d) { return { id: d.id, value: d.values[d.values.length - 1] }; })
            .attr("transform", function (d) { return "translate(" + x(d.value.date) + "," + y(d.value.weight) + ")"; })
            .attr("x", 3)
            .attr("dy", "0.35em")
            .attr("fill", "#AAA")
            .style("font", "10px sans-serif white")
            .text(function (d) { return d.id; });
    });

    function type(d, _, columns) {
        d.date = parseTime(d.date);
        for (var i = 1, n = columns.length, c; i < n; ++i) d[c = columns[i]] = +d[c];
        return d;
    }

}