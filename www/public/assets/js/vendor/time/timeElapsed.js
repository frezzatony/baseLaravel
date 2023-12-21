(function ($) {

  $.fn.timeElapsed = function (options) {
    var defaults = {
      currentTime: new Date(),
      full: false,
      prefix: '',
      postfix: '',
      seconds: false
    };
    var settings = $.extend({}, defaults, options);
    return this.each(function () {
      var element = $(this);
      var elementDate = new Date(element.data('time'))
      var timeObject = getTimeDifference(settings.currentTime, elementDate);
      var time = '';
      if (timeObject.year > 0) {
        if (settings.full) {
          time += `${timeObject.year}`;
          time += timeObject.year === 1 ? "Ano" : "Anos";
        } else {
          $(this).text(settings.prefix + timeObject.year + (timeObject.year === 1 ? "Ano" : "Anos") + settings.postfix).val(settings.prefix + timeObject.year + (timeObject.year === 1 ? "Ano" : "Anos") + settings.postfix);
          return;
        }
      }
      if (timeObject.month > 0) {
        if (settings.full) {
          time += (time ? ", " : " ") + `${timeObject.month}`;
          time += timeObject.month === 1 ? " Mês" : " Meses";
        } else {
          $(this).text(settings.prefix + timeObject.month + (timeObject.month === 1 ? "Mês" : "Meses") + settings.postfix).val(settings.prefix + timeObject.month + (timeObject.month === 1 ? "Mês" : "Meses") + settings.postfix);
          return;
        }
      }
      if (timeObject.day > 0) {
        if (settings.full) {
          time += (time ? ", " : " ") + `${timeObject.day}`;
          time += timeObject.day === 1 ? " Dia" : " Dias";
        } else {
          $(this).text(settings.prefix + timeObject.day + (timeObject.day === 1 ? "Dia" : "Dias ") + settings.postfix).val(settings.prefix + timeObject.day + (timeObject.day === 1 ? "Dia" : "Dias ") + settings.postfix);
          return;
        }
      }
      if (timeObject.hour > 0) {
        if (settings.full) {
          time += (time ? ", " : " ") + `${timeObject.hour}`;
          time += timeObject.hour === 1 ? " Hora" : " Horas";
        } else {
          $(this).text(settings.prefix + timeObject.hour + (timeObject.hour === 1 ? "Hora" : "Horas") + settings.postfix).val(settings.prefix + timeObject.hour + (timeObject.hour === 1 ? "Hora" : "Horas") + settings.postfix);
          return;
        }
      }
      if (timeObject.minute > 0) {
        if (settings.full) {
          time += (time ? ", " : " ") + `${timeObject.minute}`;
          time += timeObject.minute === 1 ? " Minuto" : " Minutos";
        } else {
          $(this).text(settings.prefix + timeObject.minute + (timeObject.minute === 1 ? "Minuto" : "Minutos") + settings.postfix).val(settings.prefix + timeObject.minute + (timeObject.minute === 1 ? "Minuto" : "Minutos") + settings.postfix);
          return;
        }
      }
      if (timeObject.second > 0) {
        if (settings.full) {
          time += (time ? ", " : " ") + `${timeObject.second}`;
          time += timeObject.second === 1 ? " Segundo " : " Segundos ";
        } else {
          if (timeObject.seconds) {
            $(this).text(settings.prefix + timeObject.second + (timeObject.second === 1 ? "Segundo " : "Segundos ") + settings.postfix).val(settings.prefix + timeObject.second + (timeObject.second === 1 ? "Segundo " : "Segundos ") + settings.postfix);
          } else {
            $(this).text("Agora").val("Agora");
          }
          return;
        }
      }
      $(this).text(settings.prefix + time + settings.postfix).val(settings.prefix + time + settings.postfix);
    });
  };

  function getTimeDifference(currentTime, oldTime) {

    const mTimeDifference = Math.abs(currentTime - oldTime); // Time in Milliseconds
    const sTimeDifference = mTimeDifference / 1000 // Time in Seconds
    const yearDifference = Math.floor(sTimeDifference / 31536000); // 31536000 - Average Seconds in one Year
    const monthDifference = Math.floor((sTimeDifference % 31536000) / 2592000); // 2592000 - Average Seconds in one Month (30 Days)
    const dayDifference = Math.floor((sTimeDifference % 2592000) / 86400); // 86400 - Seconds in one Day
    const hourDifference = Math.floor((sTimeDifference % 86400) / 3600); // 3600 - Seconds in one Hour
    const minuteDifference = Math.floor((sTimeDifference % 3600) / 60); // 60 - Seconds in one Minute
    const secondDifference = Math.floor(sTimeDifference % 60);

    return {
      year: yearDifference,
      month: monthDifference,
      day: dayDifference,
      hour: hourDifference,
      minute: minuteDifference,
      second: secondDifference
    };
  }
}(jQuery));
