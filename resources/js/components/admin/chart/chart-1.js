export const initChartOne = () => {
    const chartElement = document.querySelector('#chartOne');
    if (!chartElement) return;

    // Use real data if available, otherwise fallback to dummy
    const chartData = window.chartOneData || [
        { month: 'Jan 2025', count: 168 },
        { month: 'Feb 2025', count: 385 },
        { month: 'Mar 2025', count: 201 },
        { month: 'Apr 2025', count: 298 },
        { month: 'May 2025', count: 187 },
        { month: 'Jun 2025', count: 195 },
        { month: 'Jul 2025', count: 291 },
        { month: 'Aug 2025', count: 110 },
        { month: 'Sep 2025', count: 215 },
        { month: 'Oct 2025', count: 390 },
        { month: 'Nov 2025', count: 280 },
        { month: 'Dec 2025', count: 112 },
    ];

    const categories = chartData.map(item => item.month);
    const data = chartData.map(item => item.count);

    const chartOneOptions = {
        series: [{
            name: "Registrations",
            data: data,
        },],
        colors: ["#465fff"],
        chart: {
            fontFamily: "Outfit, sans-serif",
            type: "bar",
            height: 180,
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "39%",
                borderRadius: 5,
                borderRadiusApplication: "end",
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 4,
            colors: ["transparent"],
        },
        xaxis: {
            categories: categories,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        legend: {
            show: true,
            position: "top",
            horizontalAlign: "left",
            fontFamily: "Outfit",
            markers: {
                radius: 99,
            },
        },
        yaxis: {
            title: false,
        },
        grid: {
            yaxis: {
                lines: {
                    show: true,
                },
            },
        },
        fill: {
            opacity: 1,
        },

        tooltip: {
            x: {
                show: false,
            },
            y: {
                formatter: function (val) {
                    return val;
                },
            },
        },
    };

    const chart = new ApexCharts(chartElement, chartOneOptions);
    chart.render();

    return chart;
};

export default initChartOne;
