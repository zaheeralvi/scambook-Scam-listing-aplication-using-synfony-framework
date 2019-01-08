/*
 * ConnectCode
 *
 * Copyright (c) 2006-2010 ConnectCode Pte Ltd (http://www.barcoderesource.com)
 * All Rights Reserved.
 *
 * This source code is protected by International Copyright Laws. You are only allowed to modify
 * and include the source in your application if you have purchased a Distribution License.
 *
 * http://www.barcoderesource.com
 *
 */

	;(function($) {
		$.fn.Encode_Code39 = function(options) {
			var opts = $.extend({}, $.fn.Encode_Code39.defaults, options);
			return this.each(function() {
				if ($(this).text()!="")
				{
					opts.data=$(this).text();
				}
				$(this).text(ConnectCode_Encode_Code39(opts.data,opts.checkDigit,opts.humanReadableTextOutput));
			});
		};

		$.Encode_Code39 = function(data,checkDigit,humanReadableTextOutput) {
			return ConnectCode_Encode_Code39(data,checkDigit,humanReadableTextOutput);
		};
				

		function ConnectCode_Encode_Code39(data,checkDigit,humanReadableTextOutput)
		{
			var Result="";
			var cd="";
			var filtereddata="";
			filtereddata = filterInput(data);

			var filteredlength = filtereddata.length;

			if (checkDigit==1)
			{
				if (filteredlength > 254)
				{
					filtereddata = filtereddata.substr(0,254);
				}
				cd = generateCheckDigit(filtereddata);
			}
			else
			{
				if (filteredlength > 255)
				{
					filtereddata = filtereddata.substr(0,255);
				}
			}

			Result = "*" + filtereddata+cd+"*";
  		      Result=html_decode(html_escape(Result));	
			var connectcode_human_readable_text=Result;
			if (humanReadableTextOutput==1) 
				Result=connectcode_human_readable_text;
			return Result;
		}

		function getCode39Character(inputdecimal) {
			var CODE39MAP=new Array("0","1","2","3","4","5","6","7","8","9",
							"A","B","C","D","E","F","G","H","I","J",
							"K","L","M","N","O","P","Q","R","S","T",
							"U","V","W","X","Y","Z","-","."," ","$",
							"/","+","%");
			return CODE39MAP[inputdecimal];
		}

		function getCode39Value(inputchar) {
			var CODE39MAP=new Array("0","1","2","3","4","5","6","7","8","9",
							"A","B","C","D","E","F","G","H","I","J",
							"K","L","M","N","O","P","Q","R","S","T",
							"U","V","W","X","Y","Z","-","."," ","$",
							"/","+","%");
			var RVal=-1;
			for (i=0;i<43;i++)
			{
				if (inputchar==CODE39MAP[i])
				{
					RVal=i;
				}
			}
			return RVal;
		}

		function filterInput(data)
		{
			var Result="";
			var datalength=data.length;

			for (x=0;x<datalength;x++)
			{
				if (getCode39Value(data.substr(x,1)) != -1)
				{
					Result = Result + data.substr(x,1);
				}
			}
			return Result;
		}

		function generateCheckDigit(data)
		{
			var Result="";
			var datalength=data.length;
			var sumValue=0;
			for (x=0;x<datalength;x++)
			{
				sumValue=sumValue+getCode39Value(data.substr(x,1));
			}
			sumValue=sumValue % 43;
			return getCode39Character(sumValue);
		}

		function html_escape(data)
		{
			var Result="";
			for (x=0;x<data.length;x++)
			{
				Result=Result+"&#"+data.charCodeAt(x).toString()+";";
			}
			return Result;
		}

		function html_decode(str) {
			var ta=document.createElement("textarea");
		      ta.innerHTML=str.replace(/</g,"&lt;").replace(/>/g,"&gt;");
		      return ta.value;
		}

		$.fn.Encode_Code39.defaults = {
			data: "12345678",
			checkDigit: 1,
			humanReadableTextOutput: 0
		};
	})(jQuery);
