export const initChartThree = () => {
    const chartElement = document.querySelector('#chartThree');

    if (chartElement) {
        // Use real data if available, otherwise fallback
        const data = window.chartThreeData || {
            monthlyOrders: [
                { month: 'Jan 2025', total: 180 },
                { month: 'Feb 2025', total: 190 },
                { month: 'Mar 2025', total: 170 },
                { month: 'Apr 2025', total: 160 },
                { month: 'May 2025', total: 175 },
                { month: 'Jun 2025', total: 165 },
                { month: 'Jul 2025', total: 170 },
                { month: 'Aug 2025', total: 205 },
                { month: 'Sep 2025', total: 230 },
                { month: 'Oct 2025', total: 210 },
                { month: 'Nov 2025', total: 240 },
                { month: 'Dec 2025', total: 235 },
            ],
            monthlyCampaigns: [
                { month: 'Jan 2025', count: 40 },
                { month: 'Feb 2025', count: 30 },
                { month: 'Mar 2025', count: 50 },
                { month: 'Apr 2025', count: 40 },
                { month: 'May 2025', count: 55 },
                { month: 'Jun 2025', count: 40 },
                { month: 'Jul 2025', count: 70 },
                { month: 'Aug 2025', count: 100 },
                { month: 'Sep 2025', count: 110 },
                { month: 'Oct 2025', count: 120 },
                { month: 'Nov 2025', count: 150 },
                { month: 'Dec 2025', count: 140 },
            ]
        };

        const categories = data.monthlyOrders.map(item => item.month);
        const ordersData = data.monthlyOrders.map(item => item.total);
        const campaignsData = data.monthlyCampaigns.map(item => item.count);

        const chartThreeOptions = {
            series: [{
                name: "Orders Total ($)",
                data: ordersData,
            },
            {
                name: "Campaigns Created",
                data: campaignsData,
            },
            ],
            legend: {
                show: false,
                position: "top",
                horizontalAlign: "left",
            },
            colors: ["#465FFF", "#9CB9FF"],
            chart: {
                fontFamily: "Outfit, sans-serif",
                height: 310,
                type: "area",
                toolbar: {
                    show: false,
                },
            },
            fill: {
                gradient: {
                    enabled: true,
                    opacityFrom: 0.55,
                    opacityTo: 0,
                },
            },
            stroke: {
                curve: "straight",
                width: ["2", "2"],
            },
            markers: {
                size: 0,
            },
            labels: {
                show: false,
                position: "top",
            },
            grid: {
                xaxis: {
                    lines: {
                        show: false,
                    },
                },
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                x: {
                    format: "dd MMM yyyy",
                },
            },
            xaxis: {
                type: "category",
                categories: categories,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
                tooltip: false,
            },
            yaxis: {
                title: {
                    style: {
                        fontSize: "0px",
                    },
                },
            },
        };

        const chart = new ApexCharts(chartElement, chartThreeOptions);
        chart.render();
    }
}

export default initChartThree;
