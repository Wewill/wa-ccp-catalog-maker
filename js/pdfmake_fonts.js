pdfMake.fonts = {
  Roboto: {
    normal: 'BureauGrotLight.otf',
    bold: 'BureauGrotBook.otf',
    italics: 'Mrs_Eaves_XL_Serif_OT_Reg_Italic.ttf',
    bolditalics: 'Mrs_Eaves_XL_Serif_OT_Reg.ttf',
  },
  Bureau_Grot: {
    normal: 'BureauGrotLight.otf',
    bold: 'BureauGrotBook.otf',
    italics: 'Mrs_Eaves_XL_Serif_OT_Reg_Italic.ttf',
    bolditalics: 'Mrs_Eaves_XL_Serif_OT_Reg.ttf',
  },
  Bureau_Grot_Bold: {
    normal: 'BureauGrotMedium.otf',
    bold: 'BureauGrotBold.otf', //  #40 = 'BureauGrotMedium.otf',
    italics: 'Mrs_Eaves_XL_Serif_OT_Reg_Italic.ttf', // #41
    bolditalics: 'Mrs_Eaves_XL_Serif_OT_Reg.ttf', // #41
  },
  Bureau_Grot_Bolder: { 
    normal: 'BureauGrotBold.otf', // #41
    bold: 'BureauGrotBlack.otf', // #41
    italics: 'Mrs_Eaves_XL_Serif_OT_Reg_Italic.ttf', // #41
    bolditalics: 'Mrs_Eaves_XL_Serif_OT_Reg.ttf', // #41
  },
  Bureau_Grot_Condensed: {
    normal: 'BureauGrotCondensedLight.otf',
    bold: 'BureauGrotCondensedBook.otf',
  },
  Bureau_Grot_Condensed_Bold: {
    normal: 'BureauGrotCondensedBold.otf', // #41
    bold: 'BureauGrotCondensedBlack.otf', // #41
  },
  Mrs_Eaves_XL_Serif_OT: {
    normal: 'Mrs_Eaves_XL_Serif_OT_Reg.ttf',
    bold: 'Mrs_Eaves_XL_Serif_OT_Reg.ttf',
    italics: 'Mrs_Eaves_XL_Serif_OT_Reg_Italic.ttf',
    bolditalics: 'Mrs_Eaves_XL_Serif_OT_Reg_Italic.ttf',
  },
  Stereonic: {
    normal: 'Stereonic_S.ttf',
    bold: 'Stereonic_M.ttf',
  },
  Stereonic_Bold: {
    normal: 'Stereonic_L.ttf',
    bold: 'Stereonic_XL.ttf',
  },
  Stereonic_Underline: {
    normal: 'Stereonic_L_Underline.ttf',
    bold: 'Stereonic_L_Underline.ttf',
  },
  Stereonic_Doubleline: {
    normal: 'Stereonic_S_Doubleline.ttf',
    bold: 'Stereonic_M_Doubleline.ttf',
  },
  Fifam: {
    normal: 'fifam-font.ttf',
    bold: 'fifam-font.ttf',
  }
};

var SVGfontCallback = function fontCallback(family, bold, italic, fontOptions) {
  return 'Stereonic';
}
