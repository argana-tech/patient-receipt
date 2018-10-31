//
//  DateUtil.swift
//  patient_reception
//
//  Created by nishimura on 2018/10/09.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit

class DateUtil: NSObject {
    enum Format:String {
        case ymdHis = "yyyy-MM-dd HH:mm:ss"
    }
    
    public static func date2String (date:Date, format:DateUtil.Format) -> String{
        let formatter = DateFormatter()
//        formatter.dateFormat = DateFormatter.dateFormat(fromTemplate: format.rawValue, options: 0, locale: Locale(identifier: "ja_JP"))
        formatter.dateFormat = format.rawValue
        let dateString = formatter.string(from: date)
        
        return dateString
    }

}
