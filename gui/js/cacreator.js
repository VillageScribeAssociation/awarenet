/**
 *  Function to initialize the universe, to create all cells needed, link them together and
 *  set initial values to the target composite.  Returns a new universe.
 *
 *  @param  composite   {String}    Series of comma separated digits representing a number in base
 *  @param  base        {Number}    Base of number system in use
 *  @param  viewport    {String}    DOM Id of a camvas element to render intp
 */

function CACreator(composite, base, viewport) {

    var 
        universe = new CAUniverse(composite, base, viewport),   //  universe to populate
        digits = composite.split(','),                          //  array of numbers

    function addCells() {

        var i, j, cellId, newCell
            x = 0, 
            y = 0, 
            dl = digits.length,
            toAdd;

        for (i = 0; i < dl; i++) {
            x = x + 1;
            toAdd = [];

            //  add A, B and Carry cells
            universe.addCell(CASolutionCell('A' + i, x, 1));
            universe.addCell(CASolutionCell('B' + i, x, 2));
            universe.addCell(CACarryCell('C' + i, x, 3));
            
            //  add multiplication cells
            for (j = 0; j <= i; j++) {

                cellId = 'M' + i + 'x' + j;

                newCell = CAMulCell(
                    cellId,                 //  id 
                    x,                      //  x
                    4 + j,                  //  y
                    'A' + j,                //  top / A digit
                    'B' + (i - j)           //  bottom / B digit
                );

                universe.addCell(newCell);
                toAdd.push(cellId);
                
            }
            
            //  add addition tables
            //TODO


        }


    }


    /*
     *  Initialize and return universe
     */

    
    function addCells();

    return universe;

}
