<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
  <fo:layout-master-set>
    <fo:simple-page-master master-name="printmap"
	page-height="8.5in"
	page-width="11in"
	margin-top=".25in"
	margin-bottom=".25in"
	margin-left=".5in"
	margin-right=".5in">
      	<fo:region-before extent="2in"/>
	<fo:region-body margin-top=".5in"/>
    </fo:simple-page-master>
  </fo:layout-master-set>
  <fo:page-sequence master-reference="printmap">
    <fo:static-content flow-name="xsl-region-before">
          <fo:block text-align="center" line-height="20pt" space-after="10pt"><fo:inline font-weight="bold" font-size="18pt">United States 1790:  cumulative fertility rate (children ever born per 1000 women 15 to 44 years old of all marital classes)</fo:inline></fo:block>		
        </fo:static-content>
    <fo:flow flow-name="xsl-region-body">
      <fo:block space-before="15pt">
	<fo:table width="10in" height="5.5in" table-layout="fixed" border="solid black 1px">	 
	<fo:table-column 
	  column-number="1"
	  column-width="5in"/>
	  <fo:table-column
	  column-number="2"
	  column-width="2.5in"/>
	  <fo:table-body>
	     <fo:table-row>
		<fo:table-cell border="solid black 1px" padding="1em" number-rows-spanned="3">
        		<fo:block> 
 				<fo:external-graphic src="url(http://lewis.lib.virginia.edu/censusmap/testpdf/huscomap.png)"/>
			</fo:block> 
		</fo:table-cell>
		<fo:table-cell padding="2em">
		   <fo:block text-align="center">
	        	<fo:inline font-weight="bold" font-size="15pt">Legend</fo:inline>
      		   </fo:block>
		</fo:table-cell>
	     </fo:table-row>
	     <fo:table-row>
		<fo:table-cell>
        		<fo:block text-align="center"> 
 				<fo:external-graphic src="url(http://lewis.lib.virginia.edu/censusmap/testpdf/maplegend.png)"/>  
			</fo:block> 
		</fo:table-cell>
	     </fo:table-row>
	     <fo:table-row>
		<fo:table-cell display-align="after" padding="1em">
		   <fo:block>
	        	<fo:inline font-size="8pt">Retrieved from the University of Virginia Library
			</fo:inline>
      		   </fo:block>
		   <fo:block>
	        	<fo:inline font-size="8pt">Historical Census Browser 
			</fo:inline>
      		   </fo:block>
		   <fo:block>
			<fo:inline font-size="8pt">Geospatial and Statistical Data Center 
			</fo:inline>
      		   </fo:block>
		   <fo:block>
			<fo:inline font-size="7pt">http://fisher.lib.virginia.edu/collections/stats/histcensus/php/index.html
  			</fo:inline>
      		   </fo:block>
		</fo:table-cell>
               </fo:table-row>
	 </fo:table-body>
	</fo:table>
      </fo:block>
    </fo:flow>
  </fo:page-sequence>
</fo:root>
