$(function() {
  $('.call select').live('change', function() {
    var
      $call = $(this),
      $p = $call.parent().parent();
    $.ajax({
      type: 'POST',
      url: window.location,
      data: {
        sid: $p.data('sid'),
        flow: $(this).find('option:selected').val()
      },
      success: function() {
        $p.remove();
      },
      dataType: 'text'
    });
  });
  var
    calls = {},
    oldCalls,
    select = $('.template').html(),
    $calls = $('.calls'),
    updateCalls = function() {
      $.getJSON(window.location + '?json', function(data) {
        console.log('data', data);
        $.each(data, function(sid, call) {
          if(!calls[sid]) {
            calls[sid] = call;
            $calls.append('<p class="call" data-sid="' + sid + '"><span>' + [ call.to, call.from, call.time ].join('</span><span>') + '</span><span>' + select + '</span></p>');
          }
        });
        console.log('calls', calls);
        $.each(calls, function(sid, call) {
          if(!data[sid]) {
            delete calls[sid];
            $('[data-sid="' + sid + '"]').fadeOut(250, function() {
              $(this).remove();
            });
          }
        });
      });
    };
  updateCalls();
  setInterval(updateCalls, 5000);
});