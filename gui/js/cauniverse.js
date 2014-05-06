/*
 *  Requires CAViewport
 *  Requires CAHypothesis
 */


/**
 *  Universe of cellular automata
 *
 *  @param  composite   {String}    Composite number defining this universe
 *  @param  base        {Number}    Base of number system used by this universe   
 *  @param  viewport    {String}    DOM ID of a canvas element
 */

function CAUniverse(composite, base, viewport) {

    /*
     *  Private members
     */

    var
        compositeDigits = composite.split();
        base = 10,
        cells = {};
    

    /**
     *  Add a cell to the universe
     *  
     */

    function addCell(cellId, cell) {
        cells[cellId] = cell;
    }

    /*
     *  Return API to this universe
     */

    return {
        'addCell': addCell
    }

}

