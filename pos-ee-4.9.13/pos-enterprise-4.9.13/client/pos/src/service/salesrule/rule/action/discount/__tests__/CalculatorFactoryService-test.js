import CalculatorFactoryService from '../CalculatorFactoryService';
import ByFixedService from '../ByFixedService';

describe('Test calculatior factory service', () => {

  it('[DIS-CFS-01] Create calculator for empty type', () => {
    expect(CalculatorFactoryService.create('')).toEqual(undefined);
  });

  it('[DIS-CFS-02] Create calculator for by_fixed type', () => {
    expect(CalculatorFactoryService.create('by_fixed')).toEqual(ByFixedService);
  });

  it('[DIS-CFS-03] Create calculator for none type', () => {
    expect(CalculatorFactoryService.create('none')).toEqual(undefined);
  });
});
