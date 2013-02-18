
var timerId;

$(document).ready(function ()
{
    StartTimer();
    $(".NextDealLink").click(function ()
    {
        //console.log("next");
        Refresh("next");
        return false;
    });
    $(".PreviousDealLink").click(function ()
    {
        //console.log("previous");
        Refresh("previous");
        return false;
    });
    $("a").attr("target", "_blank");


    $(".textoverflow").dotdotdot({
        ellipsis: '... ',
        watch: true,
    });
});

function StartTimer()
{
    timerId = setTimeout(function () { Refresh("next"); }, 5000);
}

function Refresh(direction)
{
    console.log("refresh: " + direction);
    window.clearTimeout(timerId);

    var ads = $(".WidgetAd");
    //console.log("ads: " + ads.length);


    for (var i = 0; i < ads.length; i++)
    {
        var ad = $(ads[i]);
        var displayState = ad.css("display");
        //console.log("display: " + displayState);
        if (displayState == "block")
        {
            var next;
            if (direction == "next")
            {
                next = i + 1;
                if (next == ads.length)
                {
                    next = 0;
                }
            }
            else
            {
                next = i - 1;
                if (next < 0)
                {
                    next = ads.length - 1;
                }
            }


            var nextAd = $(ads[next]);
            ad.fadeOut("fast", function ()
            {
                nextAd.fadeIn("fast");
            });

            StartTimer();
            break;
        }

    }


}