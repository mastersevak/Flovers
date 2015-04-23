
var Resample = (function (canvas) {

    // (C) WebReflection Mit Style License

    function Resample(img, width, height, onresample) {
        var
            load = typeof img == "string",
            i = load || img;

        // if string, a new Image is needed
        if (load) {
            i = new Image;
            i.onload = onload;
            i.onerror = onerror;
        }

        i._onresample = onresample;
        i._width = width;
        i._height = height;
        load ? (i.src = img) : onload.call(img);
    }

    function calc(imgW, imgH, destW, destH){
        //если передан один параметр
        if(!destW && destH) {
            return [null, imgH];
        }
        else if(!destH && destW){
            return [imgW, null];
        }
        else if(destW && destH) {

            iq = imgH / imgW;
            oq = destH / destW;

            if(oq < 1){ // широкий выход
                if(iq < 1){ // широкий оригинал
                    if(iq<oq){
                        return [null, imgH];
                    }
                    else{
                        return [imgW, null];
                    }
                }
                else{ // узкий или квадратный оригинал
                    return [imgW, null];
                }
            }

            else if(oq > 1){ // узкий выход
                if(iq > 1){ // узкий оригинал
                    if(iq<oq)
                        return [null, imgH];
                    else
                        return [imgW, null];
                }
                else // широкий или квадратный оригинал
                    return [null, imgH];
            }
            
            else{ //квадратный выход
                if(iq<1) // широкий оригинал
                    return [null, imgH];
                else // узкий оригинал
                    return [imgW, null];
            }

        }
    }

    function onerror() {
        throw ("not found: " + this.src);
    }

    function onload() {
        var
            img = this,
            width = img._width,
            height = img._height,
            onresample = img._onresample
            ;
        
        // if width and height are both specified
        // the resample uses these pixels
        // if width is specified but not the height
        // the resample respects proportions
        // accordingly with orginal size
        // same is if there is a height, but no width
        delete img._onresample;
        delete img._width;
        delete img._height;

        // when we reassign a canvas size
        // this clears automatically
        // the size should be exactly the same
        // of the final image
        // so that toDataURL ctx method
        // will return the whole canvas as png
        // without empty spaces or lines
        canvas.width = width;
        canvas.height = height;
        // drawImage has different overloads
        // in this case we need the following one ...
        
        output = calc(img.width, img.height, width, height);
        
        if(!output[0]){
           output[0] = width * output[1] / height;
           x = (img.width - output[0]) / 2;
           y = 0;
        }

        if(!output[1]){
            output[1] = output[0] * height / width;
            x = 0;
            y = (img.height - output[1]) / 2;
        }

        context.drawImage(
            // original image
            img,
            // starting x point
            x,
            // starting y point
            y,
            // image width
            output[0],
            // image height
            output[1],
            // destination x point
            0,
            // destination y point
            0,
            // destination width
            width,
            // destination height
            height
        );

        // retrieve the canvas content as
        // base4 encoded PNG image
        // and pass the result to the callback
        onresample(canvas.toDataURL("image/png"));
    }

    var context = canvas.getContext("2d"),
    // local scope shortcut
    round = Math.round
    ;

    return Resample;

}(
    this.document.createElement("canvas"))
);