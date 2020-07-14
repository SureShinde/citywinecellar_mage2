import extension from "../Extension";

describe('Extension rewrite and plugin', () => {
  afterEach(() => {
    extension.generatedClasses = {};
  });
  it('Rewrite a class', () => {
    extension.ExtensionConfig = {};
    class P {
      static className = 'P';
      method() {
        return 1;
      }
    }
    let Class = extension.get('service', P);
    expect(Class).toBe(P);
    expect((new Class()).method()).toBe(1);
    extension.generatedClasses = {};

    extension.ExtensionConfig = {
      rewrite: {
        service: {
          P: function(P) {
            return class C extends P {
              method() {
                return 2;
              }
            }
          }
        }
      }
    };
    Class = extension.get('service', P);
    expect(Class).not.toBe(P);
    expect((new Class()).method()).toBe(2);
  });

  it('Mixin a class', () => {
    extension.ExtensionConfig = {
      rewrite: {service: {}},
      mixin: {
        service: {
          Mixin: {
            method: function(order) {
              order.push('MixinClass');
              this.order = order;
              return order;
            },
            getOrder: function() {
              return this.order;
            },
            static: {
              plus: function(a, b) {
                return b + a;
              }
            }
          },
        }
      }
    };
    class Mixin {
      static className = 'Mixin';
    }
    expect(Mixin.method).toBeUndefined();
    expect(Mixin.plus).toBeUndefined();

    let Class = extension.get('service', Mixin);
    expect(Class).toBe(Mixin);

    let mixin = new Mixin();
    expect(mixin.method([])).toEqual(['MixinClass']);
    expect(mixin.getOrder()).toEqual(['MixinClass']);
    expect(Mixin.plus(1, 2)).toBe(3);
  });

  it('Plugin a method', () => {
    extension.ExtensionConfig = {
      plugin: {
        service: {
          P: {
            method: {
              one: {
                sortOrder: 1,
                disabled: false,
                before: (order) => {
                  order.push('1B');
                  return order;
                },
                after: (result, order) => {
                  result.push('1A');
                  return result;
                }
              },
              two: {
                sortOrder: 2,
                disabled: false,
                before: (order) => {
                  order.push('2B');
                  return order;
                },
                around: (proceed, order) => {
                  order.push('2AB');
                  order = proceed(order);
                  order.push('2AA');
                  return order;
                },
                after: (result, order) => {
                  result.push('2A');
                  return result;
                }
              },
              three: {
                sortOrder: 3,
                disabled: false,
                before: (order) => {
                  order.push('3B');
                  return order;
                },
                around: (proceed, order) => {
                  order.push('3AB');
                  order = proceed(order);
                  order.push('3AA');
                  return order;
                },
                after: (result, order) => {
                  result.push('3A');
                  return result;
                }
              },
              four: {
                sortOrder: 4,
                disabled: false,
                before: (order) => {
                  order.push('4B');
                  return order;
                },
              },
              five: {
                sortOrder: 5,
                disabled: false,
                after: (result, order) => {
                  result.push('5A');
                  return result;
                }
              },
              six: {
                sortOrder: 6,
                disabled: true,
                after: (result, order) => {
                  result.push('6A');
                  return result;
                }
              },
            }
          }
        }
      }
    };
    class P {
      static className = 'P';
      method(order) {
        order.push('P');
        return order;
      }
    }
    let Class = extension.get('service', P);
    expect(Class).toBe(P);
    expect((new Class()).method([])).toEqual([
      '1B',
        '2B',
        '2AB',
          '3B',
          '3AB',
            '4B',
              'P',
            '5A',
          '3AA',
          '3A',
        '2AA',
        '2A',
      '1A',
    ]);
  });

  it('Plugin a static method', () => {
    extension.ExtensionConfig = {
      rewrite: {service: {}},
      plugin: {
        service: {
          P: {
            method: {
              one: {
                sortOrder: 1,
                disabled: false,
                before: (order) => {
                  order.push('B');
                },
                around: (proceed, order) => {
                  order.push('AB');
                  order = proceed(order);
                  order.push('AA');
                  return order;
                },
                after: (result, order) => {
                  result.push('A');
                  return result;
                }
              },
            },
            plus: {
              one: {
                sortOrder: 1,
                disabled: false,
                before: (a, b) => {
                  return [a + 1, b + 1];
                },
              }
            }
          }
        }
      }
    };
    class P {
      static className = 'P';
      static method(order) {
        order.push('P');
        return order;
      }
      static plus(a, b) {
        return b + a;
      }
    }
    let Class = extension.get('service', P);
    expect(Class).toBe(P);
    expect(Class.method([])).toEqual([
      'B',
      'AB',
        'P',
      'AA',
      'A',
    ]);
    expect(Class.plus(1, 2)).toBe(5);
  });
});
