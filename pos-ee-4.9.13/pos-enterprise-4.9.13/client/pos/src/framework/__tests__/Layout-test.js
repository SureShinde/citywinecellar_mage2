import layout from "../Layout";
import {updateConfig} from "../../extension/config"

describe('Test layout mechanism', () => {
  it('Get layout function with dot pattern', () => {
    updateConfig({});
    expect(layout('catalog')()(1)).toBeUndefined();
    expect(layout('catalog.product.category')()).toBe(layout('catalog')('product')('category')());
    updateConfig({
      layout: {}
    });
    expect(layout('catalog.product.category')()).toBe(layout('catalog')('product')('category')());
    updateConfig({
      layout: {
        catalog: {
          product: {
            category: () => 10
          }
        }
      }
    });
    expect(layout('catalog.product')()).toBe(layout('catalog')('product')());
    expect(layout('catalog.product.category')()()).toBe(10);
    expect(layout('catalog.product.category')()).toBe(layout('catalog')('product')('category')());
  });

  it('Execute layout by plugin config', () => {
    updateConfig({
      layout: {
        catalog: {
          product: {
            category: (x) => 10 + x,
            page_size: 16,
            test: [
              (x) => x + 1,
              (x) => x + 2,
              13,
            ]
          }
        }
      }
    });
    expect(layout('catalog.product.category')()(11)).toBe(21);
    expect(layout('catalog')('product')('category')()(10)).toBe(20);
    expect(layout('catalog')('product')('page_size')()).toBe(16);
    expect(layout('catalog')('product')('test')()(10)).toEqual([11, 12, 13]);
  });
});
