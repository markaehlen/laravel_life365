<template>
  <div id="figureCanvas">
        <v-stage ref="slsxStage" :config="configKonva">
        </v-stage>
    </div>
</template>

<script>

export default {
  inheritAttrs: false,
  props: {
      units: String,
      concUnits: String,
      justSection: Boolean,
      plotType: String,
      maxConc: Number,
      allPoints: Array,
      thickness: Number,
      clearCover: Number,
      iCurrentlyDisplayedMonth: Number,
    projectData: Object,
  },
  data() {
    return {
      configKonva: {
        width: 1000,
        height: 200,
      },
      dFactor: 1.0,
    //   plotType: this.plotType,
    maxModelDepth:null,
    currentlyDisplayedMonth:null,
    dDepthToServiceLifeModeledLine:null,
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      length_unit: null,
      standard_length_unit: null,
      font_size_nrml: 15,
      font_size_small: 15,
    }
  },
  mounted() {
    // this.thickness=300;
    var stage = this.$refs.slsxStage.getNode();
      // var alllayer = new Konva.Layer();
    // var circle = new Konva.Circle({
    //     x: 100,
    //     y: 100,
    //     radius: 70,
    //     fill: 'red',
    //     stroke: 'black',
    //     strokeWidth: 4,
    //   });
    //   layer.add(circle);
    //   stage.add(layer);
    var monthmatrixlayer = new Konva.Layer();
    this.currentlyDisplayedMonth=this.iCurrentlyDisplayedMonth;
    const container = document.querySelector('#figureCanvas')
    this.configKonva.width = container.offsetWidth
if (this.plotType=="slabs and walls (1-D)") {
    this.configKonva.height = (container.offsetWidth*0.30>container.offsetHeight?container.offsetHeight:container.offsetWidth*0.30)
}else{
  // this.configKonva.height = container.offsetHeight
  this.configKonva.height = (container.offsetWidth*0.70>container.offsetHeight?container.offsetHeight:container.offsetWidth*0.70)
}

if(this.configKonva.height<'100'){
  this.font_size_nrml=12;
}
    if(this.concUnits=="kg/cub. m."){
        this.dFactor=this.convertPctConc_to_KgPerM3(1.0)
    }
    if(this.concUnits=="lb/cub. yd."){
        this.dFactor=this.convertPctConc_to_LbsPerY3(1.0)
    }

    this.maxModelDepth=this.getMaximumModelableDepthGivenConcTypeAndUnits(this.projectData)
    if(this.thickness>this.maxModelDepth){
        this.dDepthToServiceLifeModeledLine=this.maxModelDepth
    }
    
    this.getRedefinedUnits(this.projectData.baseUnits)

    // ------------------
    if (this.currentlyDisplayedMonth == 0) {
       this.currentlyDisplayedMonth = 1;
     }

    var TOP_BORDER = parseInt(this.configKonva.height / 5.0);
    var BOTTOM_BORDER = parseInt(this.configKonva.height / 5.0);
    var SIDE_BORDER = 2 * BOTTOM_BORDER;
    var RIGHT_SIDE_INSET = parseInt(this.configKonva.height / 15.0);
    var ONE_HALF_SCRATCH_TICK = RIGHT_SIDE_INSET / 3;
    var topOfConc = 0, bottomOfConc = 0, leftOfConc = 0, centerX = 0, rightOfConc = 0, widthOfConc = 0;
    var heightOfConc = 0, circleDiam = 0, rebarLayoutDiam = 0;
    var rebarDiam = 5;

    // -------- step 1 base shape creating ------

     if (this.plotType=="slabs and walls (1-D)") {
       topOfConc = TOP_BORDER;
       bottomOfConc = this.configKonva.height - BOTTOM_BORDER;
       leftOfConc = parseInt(SIDE_BORDER * 2.0);
       rightOfConc = this.configKonva.width - parseInt(SIDE_BORDER * 1.7);
       widthOfConc = rightOfConc - leftOfConc;
       heightOfConc = bottomOfConc - topOfConc; 

       monthmatrixlayer.add(new Konva.Rect({
          x: leftOfConc,
          y: topOfConc,
          width: widthOfConc,
          height: heightOfConc,
        //   shadowBlur: 10,
        //   fill: '#D3D3D3',
          stroke: 'black',
          strokeWidth: 1,
        }));
        var  heightOfReinfLine = parseInt(Math.abs(topOfConc - bottomOfConc) * this.clearCover / this.thickness);
        var  heightOfSlLine = parseInt(Math.abs(topOfConc - bottomOfConc) * this.dDepthToServiceLifeModeledLine / this.thickness);
       if (this.dDepthToServiceLifeModeledLine < this.thickness && this.dDepthToServiceLifeModeledLine > 0.0) {
         var  horzCenterLine = parseInt((leftOfConc + rightOfConc) / 2.0);
         
         monthmatrixlayer.add(new Konva.Line({
            points: [horzCenterLine - 5, topOfConc + heightOfSlLine, horzCenterLine + 5, topOfConc + heightOfSlLine],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [horzCenterLine, topOfConc, horzCenterLine, topOfConc + heightOfSlLine],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [horzCenterLine + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, horzCenterLine - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [horzCenterLine + ONE_HALF_SCRATCH_TICK, topOfConc + heightOfSlLine - ONE_HALF_SCRATCH_TICK, horzCenterLine - ONE_HALF_SCRATCH_TICK, topOfConc + heightOfSlLine + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Text({
            x: horzCenterLine,
            y: topOfConc - 20,
            text: `Modeled Depth - ${this.dDepthToServiceLifeModeledLine} ${this.units}`,
            fontSize: this.font_size_nrml,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));
       } 

         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 10, topOfConc, leftOfConc - 5, topOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));

         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 10, bottomOfConc, leftOfConc - 5, bottomOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));

         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5, topOfConc, rightOfConc + 5 + 10, topOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));

         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc - RIGHT_SIDE_INSET, topOfConc + heightOfReinfLine, rightOfConc + 5 + 10, topOfConc + heightOfReinfLine],
            stroke: 'black',
            strokeWidth: 1,
         }));
       
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 5, topOfConc, leftOfConc - 5 - 5, bottomOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
         
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5 + 5, topOfConc, rightOfConc + 5 + 5, topOfConc + heightOfReinfLine],
            stroke: 'black',
            strokeWidth: 1,
         }));
      
      
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 5 + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, leftOfConc - 5 - 5 - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         
         
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 5 + ONE_HALF_SCRATCH_TICK, bottomOfConc - ONE_HALF_SCRATCH_TICK, leftOfConc - 5 - 5 - ONE_HALF_SCRATCH_TICK, bottomOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
       
       
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5 + 5 + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, rightOfConc + 5 + 5 - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5 + 5 + ONE_HALF_SCRATCH_TICK, topOfConc + heightOfReinfLine - ONE_HALF_SCRATCH_TICK, rightOfConc + 5 + 5 - ONE_HALF_SCRATCH_TICK, topOfConc + heightOfReinfLine + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
        monthmatrixlayer.add(new Konva.Text({
            x: leftOfConc - 30,
            y: (topOfConc + bottomOfConc) * 0.7,
            text: parseFloat(this.thickness).toFixed(2) + " " + this.units,
            fontSize: this.font_size_nrml,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
            rotation: 270,
         }));
        monthmatrixlayer.add(new Konva.Text({
            x: rightOfConc + 5 + 10,
            y: topOfConc + heightOfReinfLine / 2-10,
            text: parseFloat(this.clearCover).toFixed(2) + "\n" + this.units,
            fontSize: this.font_size_nrml,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));

    }else if(this.plotType=="square column/beams (2-D)") {
       topOfConc = TOP_BORDER;
       bottomOfConc = this.configKonva.height - BOTTOM_BORDER;0       
       leftOfConc = parseInt((this.configKonva.width - bottomOfConc - topOfConc) / 2);
       rightOfConc = this.configKonva.width - leftOfConc;
       heightOfConc = bottomOfConc - topOfConc;
       widthOfConc = heightOfConc;

       rightOfConc = leftOfConc + widthOfConc;
        monthmatrixlayer.add(new Konva.Rect({
          x: leftOfConc,
          y: topOfConc,
          width: widthOfConc,
          height: heightOfConc,
        //   shadowBlur: 10,
        //   fill: '#D3D3D3',
          stroke: 'black',
          strokeWidth: 1,
        }))
       var iDepth2Reinf = parseInt(Math.abs(topOfConc - bottomOfConc) * this.clearCover / this.thickness);
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 10, topOfConc, leftOfConc - 5, topOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 10, bottomOfConc, leftOfConc - 5, bottomOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5, topOfConc, rightOfConc + 5 + 10, topOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc - RIGHT_SIDE_INSET, topOfConc + iDepth2Reinf, rightOfConc + 5 + 10, topOfConc + iDepth2Reinf],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 5, topOfConc, leftOfConc - 5 - 5, bottomOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5 + 5, topOfConc, rightOfConc + 5 + 5, topOfConc + iDepth2Reinf],
            stroke: 'black',
            strokeWidth: 1,
         }));
        
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 5 + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, leftOfConc - 5 - 5 - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [leftOfConc - 5 - 5 + ONE_HALF_SCRATCH_TICK, bottomOfConc - ONE_HALF_SCRATCH_TICK, leftOfConc - 5 - 5 - ONE_HALF_SCRATCH_TICK, bottomOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5 + 5 + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, rightOfConc + 5 + 5 - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
         
         monthmatrixlayer.add(new Konva.Line({
            points: [rightOfConc + 5 + 5 + ONE_HALF_SCRATCH_TICK, topOfConc + iDepth2Reinf - ONE_HALF_SCRATCH_TICK, rightOfConc + 5 + 5 - ONE_HALF_SCRATCH_TICK, topOfConc + iDepth2Reinf + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
       
      //  String text = String.valueOf(this.df.format(this.thickness)) + " " + this.units;
      //  g2d.drawChars(text.toCharArray(), 0, text.length(), leftOfConc - 5 - 60, (
      //      topOfConc + bottomOfConc) / 2);
      monthmatrixlayer.add(new Konva.Text({
            x: leftOfConc - 30,
            y: (topOfConc + bottomOfConc) * 0.65,
            text: parseFloat(this.thickness).toFixed(2) + " " + this.units,
            fontSize: this.font_size_nrml,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
            rotation: 270,
         }));
      //  text = String.valueOf(this.df.format(this.clearCover)) + " " + this.units;
      //  g2d.drawChars(text.toCharArray(), 0, text.length(), rightOfConc + 5 + 10, 
      //      topOfConc + iDepth2Reinf / 2);
      monthmatrixlayer.add(new Konva.Text({
            x: rightOfConc + 5 + 10,
            y: topOfConc + iDepth2Reinf / 5,
            text: parseFloat(this.clearCover).toFixed(2) + " " + this.units,
            fontSize: this.font_size_nrml,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));
     }

     else if (this.plotType=="circular columns (2-D)") {
       topOfConc = TOP_BORDER;
       bottomOfConc = this.configKonva.height - BOTTOM_BORDER;
       leftOfConc = parseInt((this.configKonva.width - bottomOfConc - topOfConc) / 2);
       rightOfConc = this.configKonva.width - leftOfConc;
       heightOfConc = bottomOfConc - topOfConc;
       widthOfConc = heightOfConc;
       
       centerX = parseInt((leftOfConc + rightOfConc) / 2);
       var centerY = parseInt((topOfConc + bottomOfConc) / 2);
       circleDiam = bottomOfConc - topOfConc;
       rebarLayoutDiam = parseInt(circleDiam * (this.thickness - this.clearCover * 2) / this.thickness);
       
      
       var outerBand = topOfConc + circleDiam / 2 - rebarLayoutDiam / 2;
      //  g2d.drawOval(parseInt(centerX - circleDiam / 2), parseInt(centerY - circleDiam / 2), circleDiam, circleDiam);
      monthmatrixlayer.add(new Konva.Ellipse({
                x: parseInt(centerX),
                y: parseInt(centerY),
                radiusX: circleDiam/2,
                radiusY: circleDiam/2,
                // fill: '#D3D3D3',
                stroke: 'black',
                strokeWidth: 1,
              }));
       
       var leftEdge = parseInt(centerX - circleDiam / 2);
       var rightEdge = parseInt(centerX + circleDiam / 2);
      monthmatrixlayer.add(new Konva.Line({
            points: [leftEdge - 5 - 10, topOfConc, leftEdge - 5, topOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
      monthmatrixlayer.add(new Konva.Line({
            points: [leftEdge - 5 - 10, bottomOfConc, leftEdge - 5, bottomOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
      monthmatrixlayer.add(new Konva.Line({
            points: [centerX + 15, topOfConc, rightEdge + 5 + 10, topOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
      monthmatrixlayer.add(new Konva.Line({
            points: [centerX + rebarLayoutDiam / 2 - 5, outerBand, rightEdge + 5 + 10, outerBand],
            stroke: 'black',
            strokeWidth: 1,
         }));
       
        monthmatrixlayer.add(new Konva.Line({
            points: [leftEdge - 5 - 5, topOfConc, leftEdge - 5 - 5, bottomOfConc],
            stroke: 'black',
            strokeWidth: 1,
         }));
        monthmatrixlayer.add(new Konva.Line({
            points: [rightEdge + 5 + 5, topOfConc, rightEdge + 5 + 5, outerBand],
            stroke: 'black',
            strokeWidth: 1,
         }));
         
        monthmatrixlayer.add(new Konva.Line({
            points: [leftEdge - 5 - 5 + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, leftEdge - 5 - 5 - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
        monthmatrixlayer.add(new Konva.Line({
            points: [leftEdge - 5 - 5 + ONE_HALF_SCRATCH_TICK, bottomOfConc - ONE_HALF_SCRATCH_TICK, leftEdge - 5 - 5 - ONE_HALF_SCRATCH_TICK, bottomOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
        monthmatrixlayer.add(new Konva.Line({
            points: [rightEdge + 5 + 5 + ONE_HALF_SCRATCH_TICK, topOfConc - ONE_HALF_SCRATCH_TICK, rightEdge + 5 + 5 - ONE_HALF_SCRATCH_TICK, topOfConc + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
        monthmatrixlayer.add(new Konva.Line({
            points: [rightEdge + 5 + 5 + ONE_HALF_SCRATCH_TICK, outerBand - ONE_HALF_SCRATCH_TICK, rightEdge + 5 + 5 - ONE_HALF_SCRATCH_TICK, outerBand + ONE_HALF_SCRATCH_TICK],
            stroke: 'black',
            strokeWidth: 1,
         }));
      //  String text = String.valueOf(this.df.format(this.thickness)) + " " + this.units;
      //  g2d.drawChars(text.toCharArray(), 0, text.length(), leftEdge - 5 - 120, (topOfConc + bottomOfConc) / 2);
      //  text = String.valueOf(this.df.format(this.clearCover)) + " " + this.units;
      //  g2d.drawChars(text.toCharArray(), 0, text.length(), rightEdge + 5 + 10, topOfConc + (bottomOfConc - topOfConc) / 10);
      monthmatrixlayer.add(new Konva.Text({
            x: leftEdge - 5 - 25,
            y: (topOfConc + bottomOfConc) * 0.7,
            text: parseFloat(this.thickness).toFixed(2) + " " + this.units,
            fontSize: this.font_size_nrml,
            rotation: 270,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));
      monthmatrixlayer.add(new Konva.Text({
            x: rightEdge + 5 + 10,
            y: topOfConc + (bottomOfConc - topOfConc) / 10 -5,
            text: parseFloat(this.clearCover).toFixed(2) + " " + this.units,
            fontSize: this.font_size_nrml,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));
     } 
 
    // -------- step 1 base shape creating ------
    // -------- step 2 Filling Month Mtrix Colors ------
    
    if (!this.justSection)
     {
      if (this.plotType=="circular columns (2-D)") {
          var minConc = 1.0E10;
          for (var tm = 0; tm < this.allPoints.length; tm++) {  
          var myMatrix = this.allPoints[tm].data;
          for (var i = 0; i < myMatrix.length; i++) {
              for (var j = 0; j < myMatrix[i].length; j++)
              {
              minConc = Math.min(minConc, myMatrix[i][j]);
              }
          } 
          } 
          var m = this.allPoints[this.currentlyDisplayedMonth - 1].data;
          minConc *= this.dFactor;
          minConc = Math.max(0.0, minConc);
          for (var c = leftEdge - 2; c < (leftEdge + rightEdge) / 2 + 2; c++) {  
          for (var r = topOfConc - 1; r < (topOfConc + bottomOfConc) / 2 + 2; r++) {    
              var colFrac = (c - leftEdge) / (rightEdge / 2 - leftEdge / 2);
              var rowFrac = (r - topOfConc) / (bottomOfConc / 2 - topOfConc / 2);
              colFrac = Math.min(1, colFrac);
              rowFrac = Math.min(1, rowFrac);    
              var matrixCol = parseInt(colFrac * m[0].length - 2);
              var matrixRow = parseInt(rowFrac * m.length - 2);
              matrixCol = Math.max(0, Math.min(matrixCol, m[0].length - 2));
              matrixRow = Math.max(0, Math.min(matrixRow, m.length - 2));    
              var conc = m[matrixRow][matrixCol];
              conc *= this.dFactor;
              var colorIntensity = (conc - minConc) / (this.maxConc - minConc) * 255.0;
              colorIntensity = Math.max(0.0, Math.min(255.0, colorIntensity));
              // g2d.setColor(this.getColor(colorIntensity));
              // var radius = (topOfConc + bottomOfConc) / 2.0 + 1.0 - (topOfConc + 1);
              var radius = circleDiam/2;
              var a = (leftEdge + rightEdge) / 2.0 + 1.0 - c;
              var b = (topOfConc + bottomOfConc) / 2.0 + 1.0 - r;
              var computedDistance = Math.pow(Math.pow(a, 2.0) + Math.pow(b, 2.0), 0.5);    
              if (computedDistance <= radius) {     
                // g2d.fillRect(c, r, 1, 1);
                monthmatrixlayer.add(new Konva.Rect({
                    x: c,
                    y: r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
                // g2d.fillRect(rightEdge - c - leftEdge + 1, r, 1, 1);
                monthmatrixlayer.add(new Konva.Rect({
                    x: leftEdge+rightEdge - c,
                    y: r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
                // // g2d.fillRect(rightEdge - c - leftEdge + 1, bottomOfConc - r - topOfConc + 1, 1, 1);
                monthmatrixlayer.add(new Konva.Rect({
                    x: leftEdge+rightEdge - c,
                    y: topOfConc+bottomOfConc - r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
                monthmatrixlayer.add(new Konva.Rect({
                    x: c,
                    y: topOfConc+bottomOfConc - r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
              // g2d.fillRect(c, bottomOfConc - r - topOfConc + 1, 1, 1);
              } 
          } 
          } 
      } else if (this.plotType=="square column/beams (2-D)") {
         var minConc = 1.0E10;
         for (var tm = 0; tm < this.allPoints.length; tm++) {
           var myMatrix = this.allPoints[tm].data;
           for (var i = 0; i < myMatrix.length; i++) {
             for (var j = 0; j < myMatrix[i].length; j++)
             {
               minConc = Math.min(minConc, myMatrix[i][j]);
             }
           } 
         }
        var m = this.allPoints[this.currentlyDisplayedMonth - 1].data;
        minConc *= this.dFactor;         
         for (var c = leftOfConc + 1; c < (leftOfConc + rightOfConc) / 2 + 3; c++)
         {
           for (var r = topOfConc + 1; r < (topOfConc + bottomOfConc) / 2 + 3; r++)
           {
             var colFrac = (c - leftOfConc - 1) / (rightOfConc / 2 - leftOfConc / 2);
             var rowFrac = (r - topOfConc - 1) / (bottomOfConc / 2 - topOfConc / 2);
             colFrac = Math.min(1, colFrac);
             rowFrac = Math.min(1, rowFrac);             
             var matrixCol = parseInt(colFrac * m[0].length);
             var matrixRow = parseInt(rowFrac * m.length);             
             matrixCol = Math.max(0, Math.min(matrixCol, m[0].length - 2));
             matrixRow = Math.max(0, Math.min(matrixRow, m.length - 2));             
             var conc = m[matrixRow][matrixCol];
             conc *= this.dFactor;
             var colorIntensity = (conc - minConc) / (this.maxConc - minConc) * 255;
             colorIntensity = Math.max(0, Math.min(255, colorIntensity));
            //  g2d.setColor(getColor(colorIntensity));
             
            //  g2d.fillRect(c, r, 1, 1);
            monthmatrixlayer.add(new Konva.Rect({
                    x: c,
                    y: r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
            //  g2d.fillRect(rightOfConc - c - leftOfConc + 1, r, 1, 1);
            monthmatrixlayer.add(new Konva.Rect({
                    x: leftOfConc+rightOfConc - c,
                    y: r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
            //  g2d.fillRect(rightOfConc - c - leftOfConc + 1, bottomOfConc - r - topOfConc + 1, 1, 1);
            monthmatrixlayer.add(new Konva.Rect({
                    x: leftOfConc+rightOfConc - c,
                    y: topOfConc+bottomOfConc - r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
            //  g2d.fillRect(c, bottomOfConc - r - topOfConc + 1, 1, 1);
            monthmatrixlayer.add(new Konva.Rect({
                    x: c,
                    y: topOfConc+bottomOfConc - r,
                    width: 1,
                    height: 1,
                    fill: this.getColor(colorIntensity),
                }));
           }
         
         }
       
       } else if (this.plotType=="slabs and walls (1-D)") {
        let minConc = 1.0E10;
        for (let tm = 0; tm < this.allPoints.length; tm++) {
          let myMatrix = this.allPoints[tm].data;
          for (let j = 0; j < myMatrix.length; j++)
          {
            minConc = Math.min(minConc, myMatrix[j][0]);
          }
        }
        let m = this.allPoints[this.currentlyDisplayedMonth - 1].data;
        minConc *= this.dFactor;
        for (let i = topOfConc + 1; i < topOfConc + heightOfConc; i++) {           
           let percentDepth = (i - topOfConc - 1) / (topOfConc + heightOfConc - topOfConc - 1);
           let closestDepthRow = parseInt(percentDepth * m.length - 1);
           closestDepthRow = Math.max(0, Math.min(closestDepthRow, m.length - 1));
           let conc = m[closestDepthRow][0];
           conc *= this.dFactor;
           let colorIntensity = (conc - minConc) / (this.maxConc - minConc) * 255;
           colorIntensity = Math.max(0, Math.min(255, colorIntensity));
            //g2d.setColor(getColor(colorIntensity));
            //g2d.drawLine(leftOfConc + 1, i, leftOfConc + widthOfConc - 1, i);
            monthmatrixlayer.add(new Konva.Line({
                points: [leftOfConc + 1, i, leftOfConc + widthOfConc - 1, i],
                stroke: this.getColor(colorIntensity),
                strokeWidth: 1,
            }));
         } 
       }
      
     }
    
    // -------- step 2 Filling Month Mtrix Colors ------

    // -------- step 3 Adding Scale to canvas ------

    if (!this.justSection) {
             var lleft = parseInt(SIDE_BORDER * 1);
       var ltop = topOfConc;
       var lbottom = bottomOfConc;
       var lwidth = parseInt(SIDE_BORDER / 6);
       var lheight = lbottom - ltop;
       var LEGEND_SPACE = 50;
// scaleTextList
    monthmatrixlayer.add(new Konva.Rect({
        x: lleft,
        y: ltop,
        width: lwidth,
        height: lheight,
        // fill: this.getColor(colorIntensity),
        stroke: 'black',
        strokeWidth: 1,
    }));
       
       var STEPS = 6; var i;
       for (i = 0; i < 7; i++) {
         var x = lleft - 50;
         var y = ltop + parseInt(i / 6 * lheight);
         var amt = (6 - i) / 6 * this.maxConc;
        //  String text1 = this.df.format(amt);
        //  g2d.drawChars(text1.toCharArray(), 0, text1.length(), x, y + 5);
        //  g2d.drawLine(lleft - 10, y, lleft, y);
        monthmatrixlayer.add(new Konva.Text({
            x: x,
            y: y-5,
            text: parseFloat(amt).toFixed(2),
            fontSize: this.font_size_small,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));
          monthmatrixlayer.add(new Konva.Line({
              points: [lleft - 10, y, lleft, y],
              stroke: 'black',
              strokeWidth: 1,
          }));
       } 
       
       for (i = ltop + 1; i < lbottom; i++) {
         var colorIntensity = (1 - (i - ltop + 1) / (lbottom - ltop)) * 255;
         colorIntensity = Math.max(0, Math.min(255, colorIntensity));
        //  g2d.setColor(getColor(colorIntensity));
        //  g2d.drawLine(lleft + 1, i, lleft + lwidth - 1, i);
        monthmatrixlayer.add(new Konva.Line({
              points: [lleft + 1, i, lleft + lwidth - 1, i],
              stroke: this.getColor(colorIntensity),
              strokeWidth: 1,
          }));
       } 
    //    g2d.setColor(Color.black);
    //    String text = "Concentration (" + this.sConcUnits + ")";
    //    g2d.drawChars(text.toCharArray(), 0, text.length(), lleft - 2 * text.length(), ltop - 20);
    monthmatrixlayer.add(new Konva.Text({
            x: lleft - 50,
            y: ltop - 34,
            text: "Concentration \n (" + this.concUnits + ")",
            fontSize: this.font_size_small,
            fontFamily: 'Calibri',
            fill: 'black',
            align: 'center',
         }));
    }

    // -------- step 3 Adding Scale to canvas ------

    // -------- step 4 Adding Rebbers/pillers ------
    
     if (this.justSection) {
       var reberColor='black';
     } else {
       var reberColor='white';
     } 

          if (this.plotType=="slabs and walls (1-D)") {
       var depth2reinf = parseInt(Math.abs(topOfConc - bottomOfConc) * this.clearCover / this.thickness);       
    //    g2d.drawOval(leftOfConc + 25, topOfConc + depth2reinf, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
    //    g2d.drawOval(rightOfConc - 25 - RIGHT_SIDE_INSET, topOfConc + depth2reinf, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);    
    monthmatrixlayer.add(new Konva.Circle({
      x: leftOfConc + 25,
      y: topOfConc + depth2reinf + (RIGHT_SIDE_INSET/2),
      radius: RIGHT_SIDE_INSET/2,
      // fill: reberColor,
      stroke: reberColor,
      strokeWidth: 1,
    }));
    monthmatrixlayer.add(new Konva.Circle({
      x: rightOfConc - 25,
      y: topOfConc + depth2reinf + (RIGHT_SIDE_INSET/2),
      radius: RIGHT_SIDE_INSET/2,
      // fill: reberColor,
      stroke: reberColor,
      strokeWidth: 1,
    }));
    
       if (this.clearCover < 0.4 * this.thickness) {
        //  g2d.drawOval(leftOfConc + 25, bottomOfConc - depth2reinf - RIGHT_SIDE_INSET, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
        //  g2d.drawOval(rightOfConc - 25 - RIGHT_SIDE_INSET, bottomOfConc - depth2reinf - RIGHT_SIDE_INSET, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
        monthmatrixlayer.add(new Konva.Circle({
          x: leftOfConc + 25,
          y: bottomOfConc - depth2reinf - (RIGHT_SIDE_INSET/2),
          radius: RIGHT_SIDE_INSET/2,
          // fill: reberColor,
          stroke: reberColor,
          strokeWidth: 1,
        }));
        monthmatrixlayer.add(new Konva.Circle({
          x: rightOfConc - 25,
          y: bottomOfConc - depth2reinf -(RIGHT_SIDE_INSET/2),
          radius: RIGHT_SIDE_INSET/2,
          // fill: reberColor,
          stroke: reberColor,
          strokeWidth: 1,
        }));
       } else {
         var bottomDist = parseInt((topOfConc + depth2reinf + RIGHT_SIDE_INSET + bottomOfConc) / 2) + parseInt(RIGHT_SIDE_INSET / 2);
        //  g2d.drawOval(leftOfConc + 25, bottomDist - RIGHT_SIDE_INSET, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
        //  g2d.drawOval(rightOfConc - 25 - RIGHT_SIDE_INSET, bottomDist - RIGHT_SIDE_INSET, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
          monthmatrixlayer.add(new Konva.Circle({
            x: leftOfConc + 25,
            y: bottomDist,
            radius: RIGHT_SIDE_INSET/2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
          monthmatrixlayer.add(new Konva.Circle({
            x: rightOfConc - 25,
            y: bottomDist,
            radius: RIGHT_SIDE_INSET/2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
       } 
     } 
     
     else if (this.plotType=="square column/beams (2-D)") {
       var depth2reinf = parseInt(Math.abs(topOfConc - bottomOfConc) * this.clearCover / this.thickness);
    //    g2d.drawOval(leftOfConc + depth2reinf, topOfConc + depth2reinf, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
    //    g2d.drawOval(rightOfConc - depth2reinf - RIGHT_SIDE_INSET, topOfConc + depth2reinf, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
    //    g2d.drawOval(leftOfConc + depth2reinf, bottomOfConc - depth2reinf - RIGHT_SIDE_INSET, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
    //    g2d.drawOval(rightOfConc - depth2reinf - RIGHT_SIDE_INSET, bottomOfConc - depth2reinf - RIGHT_SIDE_INSET, RIGHT_SIDE_INSET, RIGHT_SIDE_INSET);
          monthmatrixlayer.add(new Konva.Circle({
            x: leftOfConc + depth2reinf,
            y: topOfConc + depth2reinf  + (RIGHT_SIDE_INSET/2),
            radius: RIGHT_SIDE_INSET/2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
          monthmatrixlayer.add(new Konva.Circle({
            x: rightOfConc - depth2reinf,
            y: topOfConc + depth2reinf  + (RIGHT_SIDE_INSET/2),
            radius: RIGHT_SIDE_INSET/2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
          monthmatrixlayer.add(new Konva.Circle({
            x: leftOfConc + depth2reinf,
            y: bottomOfConc - depth2reinf - (RIGHT_SIDE_INSET/2),
            radius: RIGHT_SIDE_INSET/2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
          monthmatrixlayer.add(new Konva.Circle({
            x: rightOfConc - depth2reinf,
            y: bottomOfConc - depth2reinf - (RIGHT_SIDE_INSET/2),
            radius: RIGHT_SIDE_INSET/2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
     } 
     
     else if (this.plotType=="circular columns (2-D)") {
       centerX = parseInt((leftEdge + rightEdge) / 2);
       var centerY = parseInt((topOfConc + bottomOfConc) / 2);
       circleDiam = bottomOfConc - topOfConc;
      //  rebarLayoutDiam = parseInt(circleDiam * (this.thickness - this.clearCover * 2) / this.thickness) - 5;
       for (var i = 0; i < 16; i++) {
         var radian = (i / 16) * 2 * Math.PI;
         var x = parseInt(centerX + Math.sin(radian) * rebarLayoutDiam / 2);
         var y = parseInt(centerY - Math.cos(radian) * rebarLayoutDiam / 2);
         var width = 5, height = 5;
        //  g2d.drawOval(x, y, 5, 5);
          monthmatrixlayer.add(new Konva.Circle({
            x: x,
            y: y,
            radius: 2,
            // fill: reberColor,
            stroke: reberColor,
            strokeWidth: 1,
          }));
       } 
     } 
    // -------- step 4 Adding Rebbers/pillers ------

    stage.add(monthmatrixlayer)
    // ------------------
  },
  created() {
  },
  methods: {
    convertPctConc_to_KgPerM3(value) {
        return 2350.0 * value / 100.0;
    },
    convertPctConc_to_LbsPerY3(value) {
        return 3949.0 * value / 100.0;
    },
    getRedefinedUnits(baseUnits) {
      axios.get(`/change-units-string/${baseUnits}`).then((response) => {
        const { data } = response
        this.area_unit = data.area_unit
        this.volume_unit = data.volume_unit
        this.weight_unit = data.weight_unit
        this.capacity_unit = data.capacity_unit
        this.length_unit = data.length_unit
        this.standard_length_unit = data.standard_length_unit
      })
    },
    getMaximumModelableDepthGivenConcTypeAndUnits(project_data){
        var pd=project_data;
        if (pd.typeOfStructure=="slabs and walls (1-D)") {
            if (pd.baseUnits=="US units")
              {
                return 10.0; 
              }
            if (pd.baseUnits=="Centimeter metric")
              {
                return 25.0;
              }
          return 250.0;
        }
        
        if (pd.baseUnits=="US units")
            {
              return 100.0;
            }
        if (pd.baseUnits=="Centimeter metric")
            {
              return 250.0;
            }
        return 2500.0; 
    },
    getColor(colorIntensity){
        if (colorIntensity >= 123.0) {
            var scaleValue = Math.min(1.0, (colorIntensity - 123.0) / 123.0);
            green = parseInt(scaleValue * 255.0);
            red = 255 - parseInt(scaleValue * 255.0);
            blue = 0;
        }
        else {
            var scaleValue = Math.max(0.0, (colorIntensity - 0.0) / 122.0);
            red = parseInt(scaleValue * 255.0);
            blue = 255 - parseInt(scaleValue * 255.0);
            green = 0;
        } 
        
        var red = Math.min(Math.max(0, red), 255);
        var green = Math.min(Math.max(0, green), 255);
        var blue = Math.min(Math.max(0, blue), 255);
        
        return `rgb(${red}, ${green}, ${blue})`;
        }
  },
}
</script>
