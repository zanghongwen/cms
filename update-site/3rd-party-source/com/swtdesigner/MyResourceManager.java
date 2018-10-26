package com.swtdesigner;

import java.util.HashMap;
import java.util.Map;

import org.eclipse.swt.SWT;
import org.eclipse.swt.graphics.Font;
import org.eclipse.swt.graphics.FontData;
import org.eclipse.swt.widgets.Display;

public class MyResourceManager {

  /**
   * Maps fonts to their italic versions.
   */
  private static Map<Font, Font> fontToItalicFontMap = new HashMap<Font, Font>();

  /**
   * Returns an italic version of the given {@link Font}.
   * 
   * @param baseFont
   *          the {@link Font} for which an italic version is desired
   * @return the italic version of the given {@link Font}
   */
  public static Font getItalicFont(Font baseFont) {
    Font font = fontToItalicFontMap.get(baseFont);
    if (font == null) {
      FontData fontDatas[] = baseFont.getFontData();
      FontData data = fontDatas[0];
      font = new Font(Display.getCurrent(), data.getName(), data.getHeight(), SWT.ITALIC);
      fontToItalicFontMap.put(baseFont, font);
    }
    return font;
  }

  public static void disposeFonts() {
    // clear italic fonts
    for (Font font : fontToItalicFontMap.values()) {
      font.dispose();
    }
    fontToItalicFontMap.clear();
  }

}
