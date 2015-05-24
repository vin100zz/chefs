function drawTimeline(cfg) {

  // config
  var x0 = 20;
  var y0 = 0;
  
  var marginAroundYears = 20;
  
  var horizontalMarginAroundNames = 20;
  var verticalMarginAroundNames = 10;
  
  var yearWidth = 25;
  var yearHeight = 30;
  
  var nameWidth = 120;
  var nameHeight = 36;
  
  var minYear = cfg.periods[0].begin.getFullYear();
  var maxYear = new Date().getFullYear() + 1;
  
  var minTs = new Date(minYear, 0, 1).getTime();
  var oneYear = 1000 * 60 * 60 * 24 * 365;
  
  var colors = ['red', 'blue', 'green'];
  
  // cleanup  
  cfg.container.innerHTML = '';
  
  // create container
  var container = document.createElement('div');
  container.classList.add('timeline-container');
  cfg.container.appendChild(container);
  
  var timelineY = drawNames() + marginAroundYears;
  drawMarkers(timelineY);
  drawLine(timelineY);
  drawPeriods(timelineY);
  
  container.style.height = y0 + timelineY + 3*yearHeight;
  
  function drawNames() {
    var rows = [];
    
    function getRow(x) {
      var i=0;
      var end = x + nameWidth;
      for (; i<rows.length; ++i) {
        if (rows[i] + horizontalMarginAroundNames < x) {
          rows[i] = end;
          return i;
        }
      }
      rows.push(end);
      return i;
    }
      
    for (var i=0; i<cfg.periods.length; ++i) {
      var period = cfg.periods[i];
      
      var begin = period.begin;
      var end = isNaN(period.end.getTime()) ? new Date(maxYear, 0, 1) : period.end;
      
      var xPeriod = x0 + (begin.getTime() - minTs) * yearWidth / oneYear;
      var width = (end.getTime() - begin.getTime()) * yearWidth / oneYear;
      
      period.x = xPeriod + (width - nameWidth) / 2;
      period.row = getRow(period.x);
    }
    
    for (var i=0; i<cfg.periods.length; ++i) {
      var period = cfg.periods[i];
      
      period.y = y0 + (rows.length - period.row - 1) * (nameHeight + verticalMarginAroundNames);
      
      var metaDiv = document.createElement('div');
      metaDiv.classList.add('label-name-meta');
      metaDiv.style.top = period.y + 'px';
      metaDiv.style.left = period.x + 'px';
      metaDiv.style.width = nameWidth + 'px';
      metaDiv.style.height = nameHeight + 'px';
      metaDiv.style.borderColor = colors[i%colors.length];

        var div = document.createElement('div');
        div.classList.add('label-name');
        div.innerHTML = period.name;
        metaDiv.appendChild(div);
        
      container.appendChild(metaDiv);
    }
    
    return y0 + rows.length * (nameHeight + verticalMarginAroundNames);
  }
  
  function drawMarkers(timelineY) {
    for (var i=0; i<cfg.periods.length; ++i) {
      var period = cfg.periods[i];
      
      var div = document.createElement('div');
      div.classList.add('name-marker');
      div.style.top = (period.y + nameHeight) + 'px';
      div.style.left = (period.x + nameWidth/2) + 'px';
      div.style.width = '1px';
      div.style.height = (timelineY - (period.y + nameHeight)) + 'px';
      div.style.backgroundColor = colors[i%colors.length];
      
      var id = 'id-' + Math.ceil(Math.random() * 1000000);
      div.id = id;
      document.styleSheets[0].insertRule('.timeline-container #' + id + '.name-marker:after { background-color: ' + colors[i%colors.length] + '; }', 0);
      
      container.appendChild(div);
    }
  }
  
  function drawLine(timelineY) {
    var x = x0;
    
    for (var i=minYear; i<maxYear; ++i) {
      var div = document.createElement('div');
      div.classList.add('line-year');
      div.style.top = timelineY + 'px';
      div.style.left = x + 'px';
      div.style.width = yearWidth + 'px';
      div.style.height = yearHeight + 'px';
      container.appendChild(div);
      
      if (i%2 === 0) {
        var width = 35;
        var div = document.createElement('div');
        div.classList.add('label-year');
        div.style.top = (timelineY + yearHeight + 5) + 'px';
        div.style.left = (x - width/2) + 'px';
        div.style.width = width + 'px';
        div.innerText = i;
        container.appendChild(div);
      }
      
      x += yearWidth;
    }
    
    // arrow
    var div = document.createElement('div');
    div.classList.add('line-arrow');
    div.style.top = timelineY + 'px';
    div.style.left = x + 'px';
    container.appendChild(div);
  }
  
  function drawPeriods(timelineY) {
    var x = x0;
    
    for (var i=0; i<cfg.periods.length; ++i) {
      var period = cfg.periods[i];
      
      var begin = period.begin;
      var end = isNaN(period.end.getTime()) ? new Date(maxYear, 0, 1) : period.end;
      
      var x = x0 + (begin.getTime() - minTs) * yearWidth / oneYear;
      var width = (end.getTime() - begin.getTime()) * yearWidth / oneYear;
      
      var div = document.createElement('div');
      div.classList.add('line-period');
      div.style.top = timelineY + 'px';
      div.style.left = x + 'px';
      div.style.width = width + 'px';
      div.style.height = yearHeight + 'px';
      div.style.backgroundColor = colors[i%colors.length];
      container.appendChild(div);
    }
  }
  
  // draw
  /*var timelineY = drawEvents(x0, y0);
  drawMarkers(timelineY);
  drawYears(x0, timelineY);
  
  // set container dimensions
  container.style.height = (timelineY + 2*yearHeight + 2*marginAroundYears) + 'px';*/
  
  function parseYear(year) {
    if (typeof year === 'string') {
      var res = year.match(/\d\d\d\d/);
      return res && res.length > 0 ? parseInt(res[0], 10) : null;
    }
    return year;
  }
 
  
  
  /*function drawEvents(x0, y0) {
    var y = y0;
    
    var rows = [];
    function getRow(x, duration) {
      var i=0;
      var eventEnd = x + Math.max(eventWidth, duration);
      for (; i<rows.length; ++i) {
        if (rows[i] + horizontalMarginAroundNames < x) {
          rows[i] = eventEnd;
          return i;
        }
      }
      rows.push(eventEnd);
      return i;
    }
    
    for (var i=0; i<cfg.events.length; ++i) {
      var event = cfg.events[i];
      var eventBegin = parseYear(event.begin);
      var eventEnd = parseYear(event.end);
      if (eventBegin) {
        var x = x0 + (eventBegin - min + 0.5) * yearWidth;
        var duration = eventEnd ? (eventEnd - eventBegin) * yearWidth : 0;
        var y = y0 + getRow(x, duration) * (eventHeight + verticalMarginAroundNames);
        
        // picture
        var pictureWidth = eventHeight*4/3;
        var div = document.createElement('div');
        div.classList.add('event-picture');
        div.style.top = y + 'px';
        div.style.left = x + 'px';
        div.style.width = pictureWidth + 'px';
        div.style.height = eventHeight + 'px';
        div.style.backgroundImage = 'url(' + event.picture + ')';
        div.addEventListener('click', function() {window.location = this.link;}.bind(event));
        container.appendChild(div);
        
        // event
        var div = document.createElement('div');
        div.classList.add('event-desc');
        div.style.top = y + 'px';
        div.style.left = (x + pictureWidth) + 'px';
        div.style.width = (eventWidth - pictureWidth) + 'px';
        div.style.height = eventHeight + 'px';
        div.innerHTML = '<ul><li>' + event.name + '</li><li>' + event.begin + '</li><li>' + event.end + '</li></ul>';
        div.addEventListener('click', function() {window.location = this.link;}.bind(event));
        container.appendChild(div);
        
        // duration
        var div = document.createElement('div');
        div.classList.add('event-duration');
        div.style.top = (y + eventHeight + 3) + 'px';
        div.style.left = x + 'px';
        div.style.width = duration + 'px';
        div.style.height = '10px';
        container.appendChild(div);
        
        event.x = x;
        event.y = y;
      }
    }
    
    return y0 + rows.length * (eventHeight + verticalMarginAroundNames);
  };
  
  function drawYears(x0, y0) {
    var x = x0;
    var y = y0 + marginAroundYears;
    
    // years
    for (var i=min; i<=max; ++i) {
      var div = document.createElement('div');
      div.classList.add('year');
      div.style.top = y + 'px';
      div.style.left = x + 'px';
      div.style.width = yearWidth + 'px';
      div.style.height = yearHeight + 'px';
      div.style.lineHeight = yearHeight + 'px';
      div.innerHTML = i;
      container.appendChild(div);
      
      x += yearWidth;
    }
    
    // arrow
    var div = document.createElement('div');
    div.classList.add('year-arrow');
    div.style.top = y + 'px';
    div.style.left = x + 'px';
    container.appendChild(div);
  };*/
  

}